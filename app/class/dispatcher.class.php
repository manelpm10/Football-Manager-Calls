<?php
/**
 * Dispatcher class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Dispatcher.
 *
 * Core Dispatcher class.
 */
class Dispatcher
{
	protected $controllers; //config.ini instance.

	/**
	 * Retrieves controller configuration data.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->controllers = new Config;
		$this->controllers->start( DEFAULT_CONFIG_FILE );
	}

	/**
	 * Default dispatcher method.
	 *
	 * @return void
	 */
	public function run()
	{
		try
		{
			$this->executeController();
			$this->display();
		}
		catch ( Exception403 $e )
		{
			$e->launch();
		}
		catch ( Exception404 $e )
		{
			$e->launch();
		}
		catch ( Exception503 $e )
		{
			$e->launch();
		}
		catch ( CustomException $e )
		{
			$e->launch();
		}
		catch ( Exception $e )
		{
			try
			{
				throw new Exception503( $e->getMessage() );
			}
			catch( Exception503 $e )
			{
				$e->launch();
			}
		}
	}

	/**
	 * Executes the correct controller and the correct controller method.
	 *
	 * @return void
	 */
	protected function executeController()
	{
		$matched_controller = $this->getController();

		$controller_config  = $this->controllers->getConfig( $matched_controller, 'auth' );
		$controller 		= new $matched_controller();

		$filter_get = Factory::getInstance( 'FilterGet' );

		if ( $filter_get->exists( 'logout' ) )
		{
			$controller->logout();
		}
		elseif ( $controller_config )
		{
			$controller->login();
		}
		else
		{
			$controller->run();
		}
	}

	/**
	 * Loads the correct controller for the current request.
	 *
	 * @return mixed
	 */
	private function getController()
	{
		$filter_get = Factory::getInstance( 'FilterGet' );

		if ( $filter_get->exists( 'ctname' ) )
		{
			$response = $this->getControllerByName( $filter_get->getString( 'ctname' ) );
		}
		else
		{
			$response = $this->getControllerByUrl();
		}

		return $response;
	}

	/**
	 * Trys to find out which controller must be loaded by controller name
	 * requested by GET.
	 *
	 * @return string
	 */
	private function getControllerByName( $controller_name )
	{
		$url = Factory::getInstance( 'Url' );

		$controller_array = $this->controllers->getConfig();

		if ( Factory::getInstance( 'Filter' )->validateString( $url->getClassName() ) )
		{
			$requested_controller = $url->getClassName();

			if ( array_key_exists( $requested_controller, $controller_array ) )
			{
				$response = $this->checkControllerAccess( $requested_controller );
			}
		}

		if ( empty( $response ) )
		{
			throw new Exception404( 'Controlador no encontrado.' );
		}

		return $response;
	}

	/**
	 * Trys to find out which controller must be loaded depending on the url.
	 *
	 * @return string
	 */
	private function getControllerByUrl()
	{
		$url = Factory::getInstance( 'Url' );

		$response = $this->checkControllerAccess( $url->getClassName() );

		$controller = $url->getController();

		if ( $controller === '' )
		{
			preg_match( '/([A-Z][a-z]+)([A-Z][a-z]+)Controller/', DEFAULT_CONTROLLER, $matches );
			$url->setClassName( $matches[0], $matches[1] );
			$url->setController( $matches[0] );
			$url->setAction( $matches[1] );
			$response = $this->checkControllerAccess( DEFAULT_CONTROLLER );
		}

		if ( empty( $response ) && !isset( $controller ) )
		{
			throw new Exception404( 'Controlador no encontrado.' );
		}

		return $response;
	}

	/**
	 * Compares deny-ip and allow-ip params in the config.ini
	 * file with the client ip.
	 *
	 * @deprecated
	 * @param string $controller Controller name.
	 * @return
	 */
	private function checkControllerAccess( $controller )
	{
		return $controller;
	}

	/**
	 * Renders all the generated html.
	 *
	 * @return void
	 */
	protected function display()
	{
		Factory::getInstance( 'Template' )->render();
	}
}
