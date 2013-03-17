<?php
/**
 * Framework configuration file.
 *
 * @author Manel Pérez
 */

/* ALL PATHS */

define( 'PATH_APP', 				dirname( dirname( __FILE__ ) ) 	. DIRECTORY_SEPARATOR );
define( 'PATH_HTDOCS', 			dirname( PATH_APP ) . '/htdocs'	. DIRECTORY_SEPARATOR );
define( 'PATH_CONFIG', 			PATH_APP . 'config' 			. DIRECTORY_SEPARATOR );
define( 'PATH_LIBS', 				PATH_APP . 'libs' 				. DIRECTORY_SEPARATOR );
define( 'PATH_CONTROLLER',			PATH_APP . 'controller' 		. DIRECTORY_SEPARATOR );
define( 'PATH_MODEL', 				PATH_APP . 'model' 				. DIRECTORY_SEPARATOR );
define( 'PATH_CLASS', 				PATH_APP . 'class' 				. DIRECTORY_SEPARATOR );
define( 'PATH_TEMP', 				PATH_APP . 'tmp' 				. DIRECTORY_SEPARATOR );
define( 'PATH_TEMP_UPLOADED',		PATH_TEMP . 'uploaded' 			. DIRECTORY_SEPARATOR );
define( 'PATH_UPLOADED', 			PATH_APP . 'uploaded' 			. DIRECTORY_SEPARATOR );
define( 'PATH_I18N',				PATH_APP . 'i18n'				. DIRECTORY_SEPARATOR );
define( 'PATH_LOG',				PATH_APP . 'log'				. DIRECTORY_SEPARATOR );
define( 'PATH_CRON',				PATH_APP . 'cron'				. DIRECTORY_SEPARATOR );
define( 'PATH_CACHE', 			 	PATH_TEMP . 'cache'				. DIRECTORY_SEPARATOR );
define( 'DEFAULT_CONFIG_FILE', 	PATH_CONFIG . 'controllers.config.ini'				  );

/* SECURITY */

define( 'SECURITY_DOWNLOAD_FILE_SALT', 'aefcd81443e81a8483e142fb14dbc334679d57ef' );

/* DEFAULTS */

define( 'DEFAULT_PAGE_TITLE',		'Football Management Tool');
define( 'DEFAULT_CONTROLLER',		'IndexMatchController' );

/* DATABASE DEFAULTS */

define( 'FUTBOL_DATABASE_NAME',	'football' );

/* ENVIRONMENT */
if( isset( $_SERVER['HTTP_HOST'] ) )
{
	define( 'HTTP_HOST',				'http://' . $_SERVER['HTTP_HOST']	. DIRECTORY_SEPARATOR );

	if ( strpos( $_SERVER['HTTP_HOST'], 'walle' ) )
	{
		define( 'ENVIRONMENT',	'DEV' );
		define( 'PARTIAL_URL',	'' );
		define( 'BASE_URL',		HTTP_HOST . PARTIAL_URL );
	}
	else
	{
		define( 'ENVIRONMENT',	'PRO' );
		define( 'PARTIAL_URL',	'' );
		define( 'BASE_URL',		HTTP_HOST . PARTIAL_URL );
	}
}
else
{
	define( 'ENVIRONMENT', 'DEV' );
	define( 'BASE_URL', 'http://futbol.manelperez.com/' );
}

/* LOGIN */
define( 'LOGIN_TYPE', 'db' );

/* TEMPLATE */

define( 'PATH_TEMPLATE', 			PATH_APP . 'template' 				. DIRECTORY_SEPARATOR );
define( 'PATH_TEMPLATE_LAYOUT',		PATH_TEMPLATE . 'layout'			. DIRECTORY_SEPARATOR );
define( 'TEMPLATE_DIR', 			PATH_TEMPLATE );
define( 'TEMPLATE_COMPILE_DIR', 	PATH_TEMP . 'templates_c/' );
define( 'TEMPLATE_CONFIG_DIR',		PATH_APP  . 'configs/' );
define( 'TEMPLATE_CACHE_DIR', 		PATH_TEMP . 'cache/' );
define( 'TEMPLATE_CACHING', 		0 );
define( 'TEMPLATE_EXTENSION',		'.tpl' );
define( 'TEMPLATE_MESSAGE', 		'message' );

/* I18N */

define( 'I18N_DEFAULT_DOMAIN',		'messages' );
define( 'I18N_DEFAULT_CODESET',		'UTF-8' );
define( 'I18N_DEFAULT_LOCALE',		'es_ES' );

/* MAILS */
define( 'SUBJECT_TAG', '[FOOTBALL]' );
define( 'ADMIN_EMAIL', 'manelpm10@gmail.com' );
define( 'FROM_EMAIL', 'manelpm10@mailinator.com' );

?>
