<?php
/**
 * ReadWriteInterface class.
 *
 * @author Carlos Soriano
 * 
 */

/**
 * Interface ReadWriteInterface.
 * 
 * Read-Write Interface for set/get classes.
 * 
 */
interface ReadWriteInterface
{
	/**
	 * Sets a value for a given key.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function set( $key, $value );
	
	/**
	 * Gets a value for a given key.
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key );
	
	/**
	 * Removes a value for a given key.
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function remove( $key );
	
	/**
	 * Checks if a key already exists.
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function exists( $key );
}
