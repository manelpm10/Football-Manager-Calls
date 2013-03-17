<?php
/**
 * Controller class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Controller.
 *
 * Core Controller class.
 */
class Controller
{
	protected $model;  					//Instance of the model.
	protected $template;				//Instance of the template.
	protected $config;					//Instance of the config.
	protected $url;						//Instance of the url.
	protected $i18n;					//Instance of the i18n.

	/**
	 * Instances core classes for controller availability.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->template = Factory::getInstance( 'Template' );
		$this->url		= Factory::getInstance( 'Url' );
		$this->i18n		= Factory::getInstance( 'I18n' );

		$config_path =  PATH_CONTROLLER .
						$this->url->getController() .
						DIRECTORY_SEPARATOR .
						'config' .
						DIRECTORY_SEPARATOR .
						$this->url->getAction() .
						'.config.ini';

		$this->config = new Config;
		if ( is_readable( $config_path ) )
		{
			$this->config->start( $config_path );
		}

		$this->loadRequiredControllers();
	}

	/**
	 * Calls the given method of the given model with the given data.
	 *
	 * @param string $model_name Name of the model to call.
	 * @param string $method Name of the method to call.
	 * @param array $data Data needed for the model.
	 * @return mixed
	 */
	protected function getData( $model_name, $method, $data = NULL )
	{
		$this->loadModel( $model_name );

		return call_user_func_array( array( &$this->model[$model_name], $method ), $data );
	}

	/**
	 * Loads a model into the controller
	 *
	 * @param string $model_name Name of the model to load.
	 * @return object
	 */
	protected function loadModel( $model_name )
	{
		return $this->model[ $model_name ] = Factory::getInstance( $model_name );
	}

	/**
	 * Returns a singleton class.
	 *
	 * @param string $classname Class name to instantiate.
	 * @return object
	 */
	protected function getClass( $classname )
	{
		return Factory::getInstance( $classname );
	}

	protected function defineRequiredControllers()
	{
	}

	/**
	 * Loads the array of controllers that the main controller will use.
	 *
	 * @return bool
	 */
	protected function loadRequiredControllers()
	{
		$array_of_controllers = $this->defineRequiredControllers();

		if ( !empty( $array_of_controllers ) && !is_array( $array_of_controllers ) )
		{
			throw new ControllerException( 'You must define the required controllers
			with an array form.' );

		}
		elseif ( is_array( $array_of_controllers ) )
		{
			foreach ( $array_of_controllers as $controller_name => $values )
			{
				if ( $values['singleton'] )
				{
					Factory::getInstance( $controller_name )->run();
				}
				else
				{
					$object = new $controller_name;
					$object->run();
				}
			}
		}

		return true;
	}
}

/**
 * Class ControllerException.
 *
 * ControllerException class.
 */
class ControllerException extends CustomException
{

}
