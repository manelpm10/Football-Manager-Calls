<?php
/**
 * FormValidation class.
 *
 * @author Carlos Soriano
 */

/**
 * Class FormValidation.
 *
 * Validates form data.
 *
 * For its use, instance this class and then execute the run method, passing as 1st parameter
 * the raw $_POST data of the form, and then the specification array matching the expected
 * $_POST data. The specs array must follow the the following structure:
 *
 * $form_validation = array(
 *		'FIELD_NAME'		=> array(
 *			'required'		=> boolean,
 *			'type'			=> 'FILTER_TYPE',
 *			'match'*		=> 'field_name',
 *			'nomatch'*		=> 'value'
 *	),
 *
 * '*' keys are optional.
 *
 * Where FIELD_NAME is the "name" attribute of the form tag to validate. This is the key to an array
 * that must contain the keys 'required', and 'type'. Required is a boolean. If we set it to 'true',
 * the field won't pass validation if it is submitted empty. 'False' will allow empty values.
 *
 * FILTER_TYPE is one of the 'validateFILTER_TYPE' methods available in the Filter class. At this time we * have the following types:
 *
 * Text 		-> allows everything.
 * Alphanumeric -> allows only alphanumeric values.
 * String		-> allows characters with tilde and other non-ascii values. Also allows spaces.
 * Numeric 		-> allows only numeric values.
 * Phone		-> allows only spanish phones (starting with 6 or 9 and containing 9 numbers).
 * Email		-> allows only valid e-mails.
 * Url			-> allows only valid (must be a REAL url).
 * NumericRange -> allows a numeric range. This range is specified by 2 more keys, called 'min' or 'max'.
 * 				   You can have only a 'min' key, a 'max' key, or both at the same time. Like this:
 *
 *  $form_validation = array(
 *		'FIELD_NAME'		=> array(
 *			'required'		=> true,
 *			'type'			=> 'NumericRange',
 *			'min'			=> 1984,
 *			'max'			=> 2002
 *	),
 *
 * 'match' refers to the specified field value that the current field should match. Fi: If we have a
 * password field, and a password2 field to verify that the password is entered corectly, we should
 * use 'match' on the password specs definition like this: 'match' => 'password2'.
 *
 * 'nomatch' accepts a value. If the field receives this value, the validation will fail.
 */
class FormValidation extends Filter
{
	protected $data;	//Data to validate.
	protected $error;	//Array of ocurred errors.

	/**
	 * Initializes errors variable and localization strings.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->error = null;
		bindtextdomain( 'form_messages', PATH_I18N );
	}

	/**
	 * Runs the validation process for an array of data.
	 *
	 * @param array $data The data to validate.
	 * @param array $specs Specifications that data must comply.
	 * @return mixed
	 */
	public function run( $data, $specs )
	{
		$this->data = $data;

		foreach ( $specs as $key => $value )
		{
			$single_spec = array();

			$single_spec[0] = array($key);
			if ( is_array( $value ) )
			{
				foreach ( $value as $k => $val )
				{
					if ( is_array( $val ) )
					{
						foreach ( $val as $v )
						{
							$single_spec[0][$k][] = $v;
						}
					}
					else
					{
						$single_spec[0][$k] = $val;
					}
				}
			}
			else
			{
				$single_spec[0][$key] = $value;
			}

			$this->evaluate( $single_spec );
		}

		if ( $this->error == null )
		{
			return true;
		}

		return false;
	}

	/**
	 * Evaluates a single data field with its specification.
	 *
	 * @param array $spec Single specification data.
	 * @return void
	 */
	protected function evaluate( $spec )
	{
		if ( $this->isRequired( $spec ) && $this->isEmpty( $spec ) )
		{
			$this->error[ $spec[ 0 ][ 0 ] ] = $this->errorMessage('required');
		}
		elseif ( !$this->isMatched( $spec ) )
		{
			$this->error[$spec[0][0]] = $this->errorMessage( 'match' );
		}
		elseif ( !$this->isNotMatched( $spec ) )
		{
			$this->error[$spec[0][0]] = $this->errorMessage( 'nomatch', ucfirst( $spec[0][0] ) );
		}
		elseif ( !$this->isWithinSize( $spec ) && !$this->isEmpty( $spec ) )
		{
			$min	= ( isset( $spec[0]['min'] ) )? $spec[0]['min'] : false;
			$max	= ( isset( $spec[0]['max'] ) )? $spec[0]['max'] : false;
			$this->error[$spec[0][0]] = $this->errorMessage( 'withinsize', $min, $max );
		}

		if ( ( $error_value = $this->isValidType( $spec ) ) !== true && !$this->isEmpty( $spec ) )
		{
			switch ( $spec[0]['type'] )
			{
				case 'file':
					$accepted_string = '';
					if( isset( $spec[0]['accepted'] ) )
					{
						foreach( $spec[0]['accepted'] as $accepted )
						{
							$accepted_string .= $accepted . ', ';
						}
						$accepted_string = rtrim($accepted_string, ', ');
					}
					$error_line = sprintf( $this->errorMessage( "file$error_value" ), $accepted_string);

					break;

				case 'numericRange':
					$error_line = sprintf($this->errorMessage( 'numericRange' ), $spec[0]['min'], $spec[0]['max']);
					break;

				default:
					$error_line = $this->errorMessage($spec[0]['type']);

			}
			$this->error[ $spec[ 0 ][ 0 ] ] = $error_line;
		}
	}

	/**
	 * Evaluates if a specification is required.
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isRequired( $spec )
	{
		return ( $spec[0]['required'] ) ? true : false;
	}

	/**
	 * Evaluates whether a value matches the value of a specified field.
	 * Used for password/email verifying.
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isMatched( $spec )
	{
		if ( isset( $spec[0]['match']) )
		{
			return ( $this->data[$spec[0][0]] == $this->data[$spec[0]['match']] ) ? true : false;
		}

		return true;
	}

	/**
	 * Evaluates whether a value doesn't match the specified value.
	 * Used for password/email verifying.
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isNotMatched( $spec )
	{
		if ( isset( $spec[0]['nomatch'] ) )
		{
			return ( $this->data[$spec[0][0]] == $spec[0]['nomatch'] ) ? false : true;
		}

		return true;
	}

	/**
	 * Evaluates whether a string length is within min and max size-
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isWithinSize( $spec )
	{
		if ( isset( $spec[0]['min'] ) || isset( $spec[0]['max'] ) )
		{
			$value	= ( $spec[0]['type'] == 'Numeric' )? $this->data[$spec[0][0]] : strlen( $this->data[$spec[0][0]] );

			if ( isset( $spec[0]['min'] ) )
			{
				if ( $value < $spec[0]['min'] )
				{
					return false;
				}
			}

			if ( isset( $spec[0]['max'] ) )
			{
				if ( $value > $spec[0]['max'] )
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Evaluates if the specified specification's data field is empty.
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isEmpty( $spec )
	{
		if ( $spec[0][ 'type' ] != 'file' )
		{
			if ( isset( $this->data[ $spec[ 0 ][ 0 ] ] ) )
			{
				$data = $this->data[ $spec[ 0 ][ 0 ] ];
				if ( empty( $data ) && ( $data != 0 || $data != '0' ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}

			return true;
		}
		else
		{
			return ( empty( $this->data[ $spec[ 0 ][ 0 ] ]['name'] ) ) ? true : false;
		}

	}

	/**
	 * Evaluates if the specified specification's data field type is valid.
	 *
	 * @param array $spec Single data specification.
	 * @return bool
	 */
	protected function isValidType( $spec )
	{
		$method = 'validate' . ucfirst( $spec[ 0 ][ 'type' ] );
		if ( method_exists( $this, $method ) )
		{
			switch ( $method )
			{
				case 'validateNumericRange':
					return $this->$method( $this->data[$spec[0][0]], $spec[0]['min'], $spec[0]['max'] );
					break;

				case 'validateFile':
					if ( !isset( $spec[0]['accepted'] ) )
					{
						$spec[0]['accepted'] = array();
					}
					return $this->$method( $this->data[$spec[0][0]], $spec[0]['accepted'] );
					break;

				default:
					if ( isset( $this->data[$spec[0][0]] ) )
					{
						return $this->$method( $this->data[$spec[0][0]] );
					}
			}
		}
		else
		{
			throw new FormValidationException( 'Method "' . $method . '" does not exist.' );
		}
	}

	/**
	 * Evaluates if a File data field has an accepted data type.
	 *
	 * @param array $data File field data.
	 * @param array $accepted Accepted data types.
	 * @return bool
	 */
	protected function validateFile( $data, $accepted )
	{
		switch ( $data['error'] )
		{
			case UPLOAD_ERR_OK:
				$enters = 0;

				if( empty( $accepted ) )
				{
					return true;
				}

				foreach( $accepted as $value )
				{
					if( stristr( $data[ 'type' ], $value ) !== false )
						$enters = true;
				}

				return $enters;
				break;

			case UPLOAD_ERR_INI_SIZE || UPLOAD_ERR_FORM_SIZE:
				return 1;
				break;

			case UPLOAD_ERR_NO_FILE:
				return 3;
				break;

			case UPLOAD_ERR_PARTIAL || UPLOAD_ERR_NO_TMP_DIR || UPLOAD_ERR_CANT_WRITE:

			default:
				return 2;
				break;
		}

	}

	/**
	 * Reports the errors occurred during the validation proccess.
	 *
	 * @return mixed
	 */
	public function getFormErrors()
	{
		return $this->error;
	}

	/**
	 * Returns the correct error message.
	 *
	 * @return string
	 */
	protected function errorMessage()
	{
		$last_textdomain = textdomain( null );
		textdomain( 'form_messages' );

		$params = func_get_args();

		switch ( strtolower( $params[0] ) )
		{
			case 'required':
				$error_string = _( 'This field is required.' );
				break;

			case 'string':
				$error_string = _( 'Please, input only letters.' );
				break;

			case 'alphanumeric':
				$error_string = _( 'Please, input only alphanumeric values.' );
				break;

			case 'email':
				$error_string = _( 'Please, input a valid e-mail address.' );
				break;

			case 'url':
				$error_string = _( 'Please, input a valid URL address.' );
				break;

			case 'numeric':
				$error_string = _( 'Please, input a number.' );
				break;

			case 'numericrange':
				$error_string = _( 'You can only input a number between %1$d and %2$d.' );
				break;

			case 'withinsize':
				if ( $params[1] && $params[2] )
				{
					$error_string = sprintf( _( 'The value must be between %1$d and %2$d.' ), $params[1], $params[2] );
				}
				elseif ( $params[1] && !$params[2] )
				{
					$error_string = sprintf( _( 'The minimum value must be %1$d.' ), $params[1] );
				}
				elseif ( !$params[1] && $params[2] )
				{
					$error_string = sprintf( _( 'The maximum value must be %1$d.' ), $params[2] );
				}
				else
				{
					throw new Exception503( 'The min and max value in check numeric of are not set.' );
				}
				break;

			case 'phone':
				$error_string = _( 'Please, input a valid phone.' );
				break;

			case 'match':
				$error_string = _( 'The values doesn\'t match.' );
				break;

			case 'nomatch':
				$error_string = sprintf( _( 'This %s already exists.' ), $params[1] );
				break;

			case 'file0':
				$error_string = sprintf( _( 'Please, input only %s files.' ), $params[1] );
				break;

			case 'file1':
				$error_string = _( 'Please, input only files that are below 2 Megabytes.' );
				break;

			case 'file2':
				$error_string = _( 'There were problems uploading the file. Please, try again later.' );
				break;

			case 'file3':
				$error_string = _( 'Please, input a file.' );
				break;

			case 'text':
				$error_string = _( 'Please, input only valid text.' );
				break;

			default:
				$error_string = _( 'Please, input valid data.' );
		}

		textdomain( $last_textdomain );

		return $error_string;
	}
}

/**
 * Class FormValidationException.
 *
 * Exception Class for the FormValidation Class.
 */
class FormValidationException extends CustomException
{

}
