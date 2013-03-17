<?php
/**
 * Filter class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Filter.
 *
 * Core Filter class.
 */
class Filter
{
	protected $data;

	/**
	 * Validates some data as text.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validateText( $data )
	{
		return true;
	}

	/**
	 * Validates some data as an alphanumeric string.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validateAlphanumeric( $data )
	{
		return ( preg_match('/[^\w\i]+/', $data ) > 0 ) ? false : true;
	}

	/**
	 * Validates some data as a letters only string.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validateString( $data )
	{
		return ( preg_match('/[^0-9A-Za-z|Á-ú\-\s\i]+$/', utf8_encode( $data ) ) > 0 ) ? false : true;
	}

	/**
	 * Validates some data as a numeric value.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validateNumeric( $data )
	{
		return ( is_numeric( $data ) ) ? true : false;
	}

	/**
	 * Validates some data as a phone.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validatePhone( $data )
	{
		return ( preg_match('/^[6|9][0-9]{8}$/', $data ) == 1 ) ? true : false;
	}

	/**
	 * Validates some data as an email.
	 *
	 * @param mixed $data Data to filter.
	 * @return bool
	 */
	public function validateEmail( $data )
	{
		return ( $this->_filter_var( $data, 'email' ) ) ? true : false;
	}

	/**
	 * Validates some data as an url.
	 *
	 * @param mixed $data
	 * @return bool
	 */
	public function validateUrl( $data, $exists = true )
	{
		$response = false;
		if ( $this->_filter_var( $data, 'url' ) )
		{
			if ( $exists )
			{
				ob_start();
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $data );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				if ( curl_exec( $ch ) )
				{
					$response = true;
				}
				curl_close( $ch );
				ob_end_clean();
			}
			else
			{
				$response = true;
			}
		}

		return $response;
	}

	/**
	 * Validates a numeric range of values.
	 *
	 * @param mixed $data Data fo filter.
	 * @param integer $min Minimum value of range.
	 * @param integer $max Maximum value of range.
	 * @return bool
	 */
	public function validateNumericRange( $data, $min, $max )
	{
		return ( $min <= $data && $max >= $data ) ? true : false;
	}

	/**
	 * Gets a number from the get data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getNumber( $data )
	{
		if ( isset ( $this->data[ $data ] ) && is_array( $this->data[ $data ] ) )
		{
			foreach( $this->data[ $data ] as $value )
			{
				if ( isset ( $this->data[ $data ] ) && $this->validateNumeric( $this->data[ $data ] ) )
				{
					return $this->data[ $data ];
				}
			}

			return $this->data[ $data ];
		}
		else
		{
			if ( isset ( $this->data[ $data ] ) && $this->validateNumeric( $this->data[ $data ] ) )
			{
				return $this->data[ $data ];
			}
		}
		return null;
	}

	/**
	 * Gets an array of data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getArray( $data )
	{
		if ( isset ( $this->data[ $data ] ) )
		{
			if ( is_array( $this->data[$data] ) )
			{
				array_walk_recursive( $this->data[$data], array( $this, '_filter_xss'));
				return $this->data[$data];
			}

			return $this->_filter_xss( $this->data[ $data ] );
		}
		return null;
	}

	/**
	 * Gets a text from the get data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getText( $data )
	{
		if ( isset ( $this->data[ $data ] ) )
		{
			return $this->_filter_xss( $this->data[ $data ] );
		}
		return null;
	}

	/**
	 * Gets a string from the get data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getString( $data )
	{
		if ( isset ( $this->data[ $data ] ) && $this->validateString( $this->data[ $data ] ) )
		{
			return $this->data[ $data ];
		}
		return null;
	}

	/**
	 * Gets an alphanumeric from the get data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getAlphanumeric( $data )
	{
		if ( isset ( $this->data[ $data ] ) && $this->validateAlphanumeric( $this->data[ $data ] ) )
		{
			return addslashes( $this->data[ $data ] );
		}
		return null;
	}

	/**
	 * Gets a filtered email string from the get data.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function getEmail( $data )
	{
		if ( isset ( $this->data[ $data ] ) && $this->validateEmail( $this->data[ $data ] ) )
		{
			return $this->data[ $data ];
		}
		return null;
	}

	/**
	 * Returns all the data array.
	 *
	 * @param bool $filtered [optional] Defaults True. The returned array will be XSS filtered.
	 * @return array
	 */
	public function getAll( $filtered = true )
	{
		return $this->data;
	}

	/**
	 * Set a param.
	 *
	 * @param key
	 * @param value
	 */
	public function setData( $key, $value )
	{
		$this->data[$key] = $value;
	}

	/**
	 * Returns whether the requested variable is set or not.
	 *
	 * @param mixed $data
	 * @return bool
	 */
	public function exists( $data )
	{
		return isset( $this->data[ $data ] );
	}

	/**
	 * Filters a variable with a specified filter.
	 *
	 * @param $variable Value to filter.
	 * @param $filter Filter to use.
	 * @return mixed Returns true if the filter passes or FALSE if the filter fails.
	 */
	private function _filter_xss( $variable )
	{
		return htmlspecialchars( $variable, ENT_COMPAT, 'UTF-8' );
	}

	/**
	 * Filters a variable with a specified filter.
	 *
	 * @param $variable Value to filter.
	 * @param $filter Filter to use.
	 * @return mixed Returns true if the filter passes or FALSE if the filter fails.
	 */
	private function _filter_var( $variable, $filter )
	{
		$result = null;

		switch( $filter )
		{
			case 'url':
				if (preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $variable, $matches ) == 1 )
				{
					if ( strlen($matches[0]) == strlen($variable) )
					{
						return true;
					}
				}
				break;
			case 'email':
				if (preg_match('/^[a-zA-Z0-9!#$%&\*\+-\/\.\\?^_`{|}~]+@[a-zA-Z0-9\.]+\.[a-zA-Z]{2,4}/i', $variable, $matches ) == 1 )
				{
					if ( strlen($matches[0]) == strlen($variable) )
					{
						return true;
					}
				}
				break;
			default:
				return false;
				break;
		}

		if( empty( $result ) )
		{
			return false;
		}

		return $result;
	}
}

/**
 * Class FilterGet.
 *
 * Filters the $_GET variable.
 */
class FilterGet extends Filter
{
	/**
	 * Unsets global variable to avoid bypass of the filter class.
	 *
	 * @return bool
	 */
	public function __construct()
	{
		$this->data = $_GET;
		unset( $_GET );

		return true;
	}
}

/**
 * Class FilterPost.
 *
 * Filters the $_POST variable.
 */
class FilterPost extends Filter
{
	/**
	 * Unsets global variable to avoid bypass of the filter class.
	 *
	 * @return bool
	 */
	public function __construct()
	{
		$this->data = $_POST;
		unset( $_POST );

		return true;
	}
}

/**
 * Class FilterFiles.
 *
 * Filters the $_FILES variable.
 */
class FilterFiles extends Filter
{
	/**
	 * Unsets global variable to avoid bypass of the filter class.
	 *
	 * @return bool
	 */
	public function __construct()
	{
		$this->data = $_FILES;
		unset( $_FILES );

		return true;
	}
}

/**
 * Class FilterCookie.
 *
 * Filters the $_COOKIE variable.
 */
class FilterCookie extends Filter
{
	/**
	 * Unsets global variable to avoid bypass of the filter class.
	 *
	 * @return bool
	 */
	public function __construct()
	{
		$this->data = $_COOKIE;
		unset( $_COOKIE );

		return true;
	}
}
