<?php
/**
 * Cookie class
 * 
 * @author Carlos Soriano
 */

/**
 * Class Cookie
 * 
 * Manages cookies.
 */
class Cookie implements ReadWriteInterface
{
	/**
	 * Sets a cookie.
	 * 
	 * @param string $key Key of the cookie.
	 * @param mixed $value Value for the cookie.
	 * @param integer $expiration [optional] Time for the cookie to expire (from now).
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 3600 )
	{
		return setcookie( $key, $value, time() + $expiration, '/' );
	}
	
	/**
	 * Gets a cookie from the cookie array.
	 * 
	 * @param string $key Key of the cookie to get.
	 * @param string $filter [optional] Name of the filter method 
	 * to use before getting the value.
	 * @return mixed
	 */
	public function get( $key, $filter = false )
	{
		if ( isset( $_COOKIE[$key] ) )
		{
			if ( $filter !== false )
			{
				$filter_cookie = Factory::getInstance( 'FilterCookie' );
				return $filter_cookie->$filter( $key );
			}
			return $_COOKIE[$key];
		}
		
		return false;
	}
	
	/**
	 * Removes a cookie.
	 * 
	 * @param object $key Key of the cookie.
	 * @return bool
	 */
	public function remove( $key )
	{
		if ( isset( $_COOKIE[$key] ) )
		{
			return setcookie( $key, '', time() - 42000, '/' );
		}
		
		return false;
	}
	
	/**
	 * Checks whether a cookie exists or not.
	 * 
	 * @param object $key Key of the cookie.
	 * @return bool
	 */
	public function exists( $key )
	{
		if ( isset( $_COOKIE[$key] ) )
		{
			return true;
		}
		
		return false;
	}
}
