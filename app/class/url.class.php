<?php
/**
 * Url class.
 *
 * @author Manel Perez
 */

/**
 * Class Url.
 *
 * Core Url class.
 */
class Url
{

	/**
	 * The name to get config.
	 *
	 * @var String
	 */
	protected $class_name;

	/**
	 * This var contains the controller name.
	 *
	 * @var String
	 */
	protected $controller = '';

	/**
	 * This var contains the action to do.
	 *
	 * @var String
	 */
	protected $action;

	/**
	 * This var contains the lenguage.
	 *
	 * @var String
	 */
	protected $language;

	/**
	 * This var indentify if the system returns a clean url or get url.
	 *
	 * @var Boolean
	 */
	protected $clean_url;

	/**
	 * This var save the url to indentify the controller.
	 *
	 * @var Boolean
	 */
	protected $url;

	/**
	 * Gets the request url as default.
	 *
	 * @return bool
	 */
	public function __construct()
	{
		$filter = Factory::getInstance('FilterGet');
		if( $filter->getString( 'ctname' ) || ENVIRONMENT == 'DEV' )
		{
			$this->clean_url = false;
			$this->explodeUrlFromGet();
		}
		else
		{
			$this->clean_url = true;
			$this->explodeUrl();
		}
	}

	/**
	 * Returns the controller part.
	 *
	 * @return String.
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Set a controller.
	 *
	 * @return String.
	 */
	public function setController( $controller )
	{
		$this->controller = $controller;
	}

	/**
	 * Set an action.
	 *
	 * @return String.
	 */
	public function setAction( $action )
	{
		$this->action = $action;
	}

	/**
	 * Returns the actipn part.
	 *
	 * @return String.
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Returns the controller part.
	 *
	 * @return String.
	 */
	public function getClassName()
	{
		return $this->class_name;
	}

	/**
	 * Set the ClassName.
	 *
	 * @param String $controller The controller name.
	 * @param String $action The action for this controller.
	 * @return String.
	 */
	public function setClassName( $controller, $action )
	{
		$this->class_name = $this->buildClassName( $controller, $action );
	}

	/**
	 * Returns the language part. If language not set, return false.
	 *
	 * @return mixed String/false
	 */
	public function getLanguage()
	{
		if( !empty( $this->language ) )
		{
			return $this->language;
		}

		return false;
	}

	/**
	 * Returns the language part. If language not set, return false.
	 *
	 * @return mixed String/false
	 */
	public function setLanguage( $language )
	{
		if( !empty( $language ) )
		{
			$session = Factory::getInstance( 'Session' );

			$this->language = $language;

			if( $this->language != $session->get( 'locale' ) )
			{
				$i18n = Factory::getInstance( 'I18n' );
				$i18n->setLocale( $i18n->getNormalizedLang( $language ) );
			}
		}
	}

	/**
	 * Build an get URL or pretty URL
	 *
	 * @param String $controller The name of the controller.
	 * @param String $action The action fot this controller.
	 * @param array $params The params to set in url.
	 * @return String $url.
	 */
	public function buildUrl( $controller, $action, $params = array(), $is_absolute = true )
	{
		$controllerName = $this->buildClassName( $controller, $action );

		// Pretty URL format
		if( $this->clean_url )
		{
			$url = $this->buildCleanUrl( $controllerName, $params, $is_absolute );
		}
		// The URL with GET URL format
		else
		{
			$url = $this->buildGetUrl( $controllerName, $params, $is_absolute );
		}



		return $url;
	}

	/**
	 * Build a pretty URL.
	 *
	 * @param String $controller_name The name of the controller.
	 * @param array $params The params to set in url.
	 * @return mixed $url URL if all right. False otherwise.
	 */
	protected function buildCleanUrl( $controller_name, $params = array(), $is_absolute = true )
	{
		$config = Factory::getInstance( 'Config' );
		$config->start();
		$controller_array = $config->getConfig( $controller_name );

		$url = $controller_array['url'][0];

		if( empty( $url ) )
		{
			return false;
		}

		if( is_array( $params ) && !empty( $params ) )
		{
			foreach( $params as $value )
			{
				$url.= '/' . $value;
			}
		}

		if ( $is_absolute )
		{
			$url = BASE_URL . $url;
		}
		else
		{
			$url = PARTIAL_URL . $url;
		}

		return $url;
	}

	/**
	 * Build a GET format URL.
	 *
	 * @param String $controller_name The name of the controller.
	 * @param array $params The params to set in url.
	 * @return String $url.
	 */
	protected function buildGetUrl( $controller_name, $params = array(), $is_absolute = true )
	{
		$url = '?ctname=' . $controller_name;
		if( is_array( $params ) && !empty( $params ) )
		{
			$i = 0;
			foreach( $params as $value )
			{
				$url.= '&' . $i . '=' . urlencode( $value );
				$i++;
			}
		}

		if ( $is_absolute )
		{
			$url = BASE_URL . $url;
		}
		else
		{
			$url = PARTIAL_URL . $url;
		}

		return $url;
	}

	/**
	 * Explodes the url and set the controller, action and params from clean URL.
	 *
	 * URL Format:
	 * 		/[LANG](optional)/[RESERVED_WORD]/[PARAM_1]/[PARAM_2]/...
	 *
	 * @param string $url_string Raw url string.
	 * @return null
	 */
	protected function explodeUrl()
	{
		$res = $this->getControllerFromUrl();

		// If get a Controller from url keyword
		if( $res !== false )
		{
			$this->getParamsFromUrl();
		}
		else
		{
			$this->controller = null;
		}
	}

	/**
	 * Explodes the url and set the controller, action and params from GET URL.
	 *
	 * URL Format:
	 * 		/?ctname=[CONTROLLER]_[ACTION]&lang=[LANG_VALUE](optional)&key_1=value_1&key_2=value_2...
	 *
	 * @param string $url_string Raw url string.
	 * @return null
	 */
	protected function explodeUrlFromGet()
	{
		$get = Factory::getInstance('FilterGet');

		// Controller name
		$ctname = $get->getString( 'ctname' );

		$this->explodeCtName( $ctname );

		// PENDING! Language handler
		if( $get->getString( '0' ) == 'en' || $get->getString( '0' ) == 'es' )
		{
			// Set the language and change locale
			$this->setLanguage( $get->getString( '0' ) );
		}
		elseif ( $get->exists( 'language' ) )
		{
			// Set the language and chang locale
			$this->setLanguage( $get->getString( 'language' ) );
		}
	}

	/**
	 * Search in config.ini the url equals the request_uri. If exists, add the controller.
	 *
	 * @return boolean
	 */
	protected function getControllerFromUrl()
	{
		$config = Factory::getInstance( 'Config' );
		$config->start();
		$controller_array = $config->getConfig();

		$url			= trim( $_SERVER['REQUEST_URI'], '/' );
		$url			= str_replace( BASE_URL, '', $url );
		$url_explode	= explode( '/', $url );

		if( empty( $url ) )
		{
			// If don't get a controller use default controller
			$this->explodeCtName( DEFAULT_CONTROLLER );

			return true;
		}

		// PENDING! Language handler
		if( $url_explode[0] == 'en' || $url_explode[0] == 'es' )
		{
			// Set the language and change locale
			$this->setLanguage( $url_explode[0] );
		}

		foreach( $controller_array as $key => $value )
		{
			if( !empty( $value[ 'url' ] ) )
			{
				foreach ($value[ 'url' ] as $val )
				{
					if( empty( $val ) )
					{
						continue;
					}

					$pos = strpos( $url, $val );

					if ( $pos === 0 )
					{
						$this->explodeCtName( $key );
						$this->url = $val;
						return true;
					}
				}
			}
		}

		// If don't get a controller use default controller
		$this->explodeCtName( DEFAULT_CONTROLLER );

		return true;
	}

	/**
	 * Search in config.ini the url equals the request_uri. If exists, add the controller.
	 *
	 * @return boolean
	 */
	protected function getParamsFromUrl()
	{
		$get	= Factory::getInstance( 'FilterGet' );

		$url	= trim( $_SERVER['REQUEST_URI'], '/' );
		$start	= strlen( $this->url ) + 1;
		$params	= substr( $url, $start );

		if( !empty( $params ) )
		{
			$params = explode( '/', $params.'/' );

			$i = 0;
			foreach( $params as $param )
			{
				$get->setData( $i, $param );
				$i++;
			}
		}

		return true;
	}

	/**
	 * Build the ClassName.
	 *
	 * @param String $controller The controller name.
	 * @param String $action The action for this controller.
	 * @return String.
	 */
	public function buildClassName( $controller, $action )
	{
		$class_name = ucfirst( $action ) . ucfirst( $controller ) . 'Controller';

		return $class_name;
	}

	/**
	 * Get a controller name and split the name in controller and action and assign the values
	 * to this vars.
	 *
	 * @param String $ctname Contains the name of the controller.
	 * @return null
	 */
	protected function explodeCtName( $ctname )
	{
		preg_match( '/^([A-Z][a-z0-9]*)([A-Z][a-z0-9]*)[A-Z][a-z0-9]+/', $ctname, $matches );

		if( is_array( $matches ) && !empty( $matches ) )
		{
			$this->controller	= strtolower( $matches[2] );
			$this->action		= strtolower( $matches[1] );

			$this->setClassName( $this->controller, $this->action );
		}
	}
}
