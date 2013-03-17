<?php
/**
 * Template class.
 *
 * @author Carlos Soriano
 */

require_once '../app/libs/smarty/Smarty.class.php';
require_once '../app/libs/smarty/plugins/smarty-gettext.php';

/**
 * Class Template.
 *
 * Extends the Smarty Class.
 */
class Template extends Smarty
{
	protected	$layout			= 'layout'; //Name of the layout.
	protected	$layout_vars	= array();

	/**
	 * Initializes the Smarty configuration.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->getConfiguration();
		$this->register_block('t', 'smarty_translate');
		$this->defaultAssigns();
	}

	/**
	 * Gets the configuration from the config file.
	 *
	 * @return void
	 */
	protected function getConfiguration()
	{
		$this->template_dir = TEMPLATE_DIR;
		$this->compile_dir  = TEMPLATE_COMPILE_DIR;
		$this->config_dir   = TEMPLATE_CONFIG_DIR;
		$this->cache_dir    = TEMPLATE_CACHE_DIR;

		$this->caching		= TEMPLATE_CACHING;
	}

	/**
	 * Set default template vars.
	 */
	protected function defaultAssigns()
	{
		$session = Factory::getInstance( 'Session' );
		$this->assign( 'BASE_URL', BASE_URL );

		if ( true === $session->get( 'is_logged' ) )
		{
			$user = array(
				'is_logged'		=> $session->get( 'is_logged' ),
				'id_player'		=> $session->get( 'id_player' ),
				'username'		=> $session->get( 'username' ),
				'name'			=> $session->get( 'name' ),
				'middle_name'	=> $session->get( 'middle_name' ),
				'last_name'		=> $session->get( 'last_name' ),
				'type'			=> $session->get( 'type' ),
				'email'			=> $session->get( 'email' ),
				'role'			=> $session->get( 'role' ),
			);

			$this->assign( 'user', $user );
		}
		else
		{
			$user = array(
				'is_logged'		=> false,
				'role'			=> 'guest'
			);
			$this->assign( 'user', $user );
		}
	}

	/**
	 * Set a layout.
	 *
	 * @param String $layout The name of template file without extension.
	 * @return boolean
	 */
	public function setLayout( $layout )
	{
		$this->layout = $layout;
		return true;
	}

	/**
	 * Set a new template.
	 *
	 * @param String $template The name of template file without extension.
	 * @param String $name The name used in layoud. 'main_template' by default.
	 */
	public function setTemplate( $template, $name = 'main_template' )
	{
		$this->assign( $name,  PATH_TEMPLATE . $template . TEMPLATE_EXTENSION );
	}

	/**
	 * Render layout.
	 */
	public function render()
	{
		$url		= Factory::getInstance( 'Url' );
		$session	= Factory::getInstance( 'Session' );

		if ( !$this->getLayoutVars( 'title' ) )
		{
			$this->setLayoutVars( 'title', DEFAULT_PAGE_TITLE );
		}
		if ( !$this->getLayoutVars( 'home_link' ) )
		{
			$this->setLayoutVars( 'home_link', BASE_URL );
		}

		$this->assign( 'layout_vars', $this->layout_vars );
		$main_template = $this->get_template_vars( 'main_template' );
		if ( !empty( $main_template ) )
		{
			$this->display( PATH_TEMPLATE_LAYOUT . $this->layout . TEMPLATE_EXTENSION );
		}
	}

	/**
	 * Set a layout var.
	 *
	 * @param string $key The key to get the layout var.
	 * @param mixed $value The value to set in layout vars.
	 * @return null.
	 */
	public function setLayoutVars( $key, $value )
	{
		$this->layout_vars[$key]	= $value;
	}

	/**
	 * Get a layout var.
	 *
	 * @param string $key The key to get the layout var.
	 * @return mixed. The value or false.
	 */
	public function getLayoutVars( $key )
	{
		if ( isset ( $this->layout_vars[$key] ) )
		{
			return $this->layout_vars[$key];
		}

		return false;
	}
}

/**
 * TemplateException class.
 *
 * Exception class for the template class.
 */
class TemplateException extends CustomException
{

}
