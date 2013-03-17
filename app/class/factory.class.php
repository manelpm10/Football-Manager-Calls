<?php
/**
 * Factory class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Factory.
 *
 * Core Factory class that creates singleton objects.
 */
class Factory
{
	private static $instance; //Instance of the created objects.

	/**
	 * Creates an object if it doesn't exist and stores it.
	 *
	 * @param string $class_name Name of the class to instantiate.
	 * @return object
	 */
	public static function getInstance( $class_name, $profile = 'default' )
	{
		if ( empty( self::$instance[ $class_name ][ $profile ] ) )
		{
			self::$instance[ $class_name ][ $profile ] = new $class_name();
		}

		return self::$instance[ $class_name ][ $profile ];
	}
}
