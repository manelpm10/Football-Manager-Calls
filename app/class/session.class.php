<?php
/**
 * Session class
 *
 * @author Carlos Soriano
 */

/**
 * Class Session
 * 
 * Manages sessions.
 */
class Session implements ReadWriteInterface
{
	/**
	 * Starts a session or resumes it.
	 * 
	 * @return bool
	 */
	public function __construct()
	{
		session_start();
		
		return true;
	}
	
	/**
	 * Sets a value for a given session variable.
	 * 
	 * @param string $key Key for the array session.
	 * @param mixed $value Value for the array session.
	 * @return bool
	 */
	public function set( $key, $value )
	{
		$_SESSION[$key] = $value;
		
		return true;
	}
	
	/**
	 * Gets a value from a given session key.
	 * 
	 * @param string $key Key of the array session.
	 * @return mixed
	 */ 
	public function get( $key )
	{
		if ( isset( $_SESSION[$key] ) )
		{
			return $_SESSION[$key];
		}
		
		return false;
	}
	
	/**
	 * Removes a key from the session array.
	 * 
	 * @param string $key Key of the array session.
	 * @return bool
	 */
	public function remove( $key )
	{
		if ( isset( $_SESSION[$key] ) )
		{
			unset( $_SESSION[$key] );
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks whether the given key exists.
	 * 
	 * @param string $key Key of the array session.
	 * @return bool
	 */
	public function exists( $key )
	{
		if ( isset( $_SESSION[$key] ) )
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Ends the current session.
	 * 
	 * @return bool
	 */
	public function end()
	{
		$_SESSION = array();
		
		$cookie = Factory::getInstance( 'Cookie' );
		
		if ( $cookie->exists( session_name() ) )
		{
		    $cookie->remove( session_name() );
		}
		
		return session_destroy();
	}
}
