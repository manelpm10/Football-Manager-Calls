<?php
/**
 * Header class
 *
 * @author Carlos Soriano
 */

/**
 * Class Header
 * 
 * Manages headers.
 */
class Header implements ReadWriteInterface
{	
	protected $data; //Header data.

	/**
	 * Sets a value for a given key.
	 * 
	 * @param string $key Header type.
	 * @param mixed $value Value for the given header type.
	 * @return bool
	 */
	public function set( $key, $value )
	{
		$this->data[$key] = $value;
		
		return true;
	}
	
	/**
	 * Gets a value from a given key.
	 * 
	 * @param string $key Header type.
	 * @return mixed
	 */ 
	public function get( $key )
	{
		if ( isset( $this->data[$key] ) )
		{
			return $this->data[$key];
		}
		
		return false;
	}
	
	/**
	 * Removes a header.
	 * 
	 * @param string $key Header type.
	 * @return bool
	 */
	public function remove( $key )
	{
		if ( isset( $this->data[$key] ) )
		{
			unset( $this->data[$key] );
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks whether the given header type exists.
	 * 
	 * @param string $key Header type.
	 * @return bool
	 */
	public function exists( $key )
	{
		if ( isset( $this->data[$key] ) )
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Launches the headers.
	 * 
	 * @return bool
	 */
	public function launch()
	{
		header( implode( ';', $this->data ) );
		
		return true;
	}
}
