<?php
/**
 * Autoload function file.
 *
 * @author Carlos Soriano
 */

/**
 * Autoload function.
 *
 * @param string $classname Name of the class being searched
 * @return boolean
 */
function __autoload($classname)
{
	if( $pos = stripos( $classname, 'controller' ) )
	{
		preg_match( '/^([A-Z][a-z0-9]*)([A-Z][a-z0-9]*)[A-Z][a-z0-9]+/', $classname, $matches );
		$filename = PATH_CONTROLLER . strtolower( $matches[2] ) . DIRECTORY_SEPARATOR . strtolower( $matches[1] ) . '.ctrl.php';
	}
	elseif( $pos = stripos( $classname, 'model' ) )
	{
		$filename = PATH_MODEL . strtolower( substr( $classname, 0, $pos ) ) . '.model.php';
	}
	elseif( $pos = stripos( $classname, 'interface' ) )
	{
		preg_match( '/^([A-Z][a-z0-9]*)([A-Z][a-z0-9]*)/', $classname, $matches );

		// Search in controllers
		$filename = PATH_CONTROLLER . strtolower($matches[1]) . DIRECTORY_SEPARATOR . strtolower($matches[1]) . '.iface.php';

		if( !file_exists( $filename ) )
		{
			$filename = PATH_CLASS . strtolower( substr( $classname, 0, $pos ) ) . '.iface.php';
		}
	}
	else
	{
		$filename = PATH_CLASS . strtolower( $classname ) . '.class.php';
		
		if ( stripos( $classname, 'Filter' ) !== false )
		{
			$filename = PATH_CLASS . 'filter.class.php';
		}
		
	}
	
	if( file_exists( $filename ) ) require_once $filename;

	return true;
}

?>