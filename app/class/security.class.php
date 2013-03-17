<?php
require_once '../app/libs/aes.class.php';

/**
 * Security class
 *
 * @author Carlos Soriano
 */

/**
 * Class Security
 *
 * Manages security issues as crypting/decrypting data.
 */
class Security
{
	protected $aes; //AES instance.

	/**
	 * Generates an AES128 instance.
	 *
	 * @return object
	 */
	protected function getAesInstance()
	{
		if ( is_object( $this->aes ) )
		{
			return $this->aes;
		}

		return $this->aes = new AES( AES::AES256 );
	}

	/**
	 * Enrypts a value using AES128.
	 *
	 * @param string $value Value to encrypt.
	 * @param string $key [optional] 256bit hex-string needed to encrypt.
	 * @return string
	 */
	public static function encrypt( $value, $key = '603debaef0857d77811f352c01015ca71be2b7373b6108d72d9810a30914dff4' )
	{
		$aes = Factory::getInstance( 'Security' )->getAesInstance();

		$content = $aes->stringToHex( (String)$value );

		return $aes->encrypt( $content, $key );
	}

	/**
	 * Decrypts a value using AES128.
	 *
	 * @param string $encrypted_string AES128 encrypted string.
	 * @param string $key [optional] 256bit hex-string needed to decrypt.
	 * @return string
	 */
	public static function decrypt( $encrypted_string, $key = '603debaef0857d77811f352c01015ca71be2b7373b6108d72d9810a30914dff4' )
	{
		$aes = Factory::getInstance( 'Security' )->getAesInstance();

		$content = $aes->decrypt( $encrypted_string, $key );
		return $aes->hexToString( $content );
	}

	/**
	 * Generates a md5 hash from the given value.
	 *
	 * @param string $value Value to be hashed.
	 * @return string
	 */
	public static function hash( $value )
	{
		return md5( $value );
	}

	/**
	 * Filters an array of data against XSS injection.
	 *
	 * @param array $data Data array to be filtered.
	 * @return array Filtered array of data.
	 */
	public static function filterXss( $data )
	{
		array_walk_recursive( $data , array( Factory::getInstance( 'Security' ), 'walk_array' ) );

		return $data;
	}

	/**
	 * Callback function for the filterXss method.
	 * Does the actual filtering.
	 *
	 * @param string $value Value to be filtered.
	 * @param string $key Key of the array containing the value.
	 * @return void
	 */
	protected function walk_array( &$value, $key )
	{
		$value = htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
	}
}
