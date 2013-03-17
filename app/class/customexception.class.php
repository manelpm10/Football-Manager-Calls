<?php
/**
 * Error class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Error.
 *
 * Core Error class.
 */
abstract class CustomException extends Exception
{
	/**
	 * Logs the occurred exception into a log file.
	 *
	 * @param object $exception Exception to log.
	 * @return void
	 */
	protected function logException()
	{
		$dir_array 		= scandir( PATH_LOG );
		$filename 		= end( $dir_array );

		if( stristr( $filename, 'error' ) === false )
		{
			$filename = 'error1.log';
		}

		if ( file_exists( PATH_LOG . $filename ) )
		{
			if ( filesize( PATH_LOG . $filename ) > 1024000 )
			{
				$number = substr( $filename, 5, ( stripos( $filename, '.log' ) - 5 ) );
				$filename = 'error' . ++$number . '.log';
			}
		}

		file_put_contents( PATH_LOG . $filename, $this->parseException() . "\n\n", FILE_APPEND );
	}

	/**
	 * Returns a formated string of the received exception.
	 *
	 * @param object $exception Exception object.
	 * @return string
	 */
	public function parseException()
	{
		return '[' . date("M d Y H:i:s") . ']'
				. " '{$this->getMessage()}' in {$this->getFile()}({$this->getLine()})\n"
				. "{$this->getTraceAsString()}";
	}

	/**
	 * Outputs an error message based on the thrown exception.
	 *
	 * @param string $header Valid header string.
	 * @param string $message_type The message icon displayed to the user is based in this parameter.
	 * @return void
	 */
	protected function output( $header, $message_type )
	{
		header( $header );

		if ( is_readable( PATH_TEMPLATE . TEMPLATE_MESSAGE . TEMPLATE_EXTENSION ) )
		{
			$template = Factory::getInstance( 'Template' );
			$template->assign( 'message', $this->message );
			$template->assign( 'message_type', $message_type );
			$template->setTemplate( 'message' );
			$template->render();
		}
		else
		{
			echo $this->message;
		}
	}

	/**
	 * Runs exception treatment.
	 *
	 * @return void
	 */
	public function launch()
	{
		$this->logException();
		$this->message = 'An error occurred while making the request. Please try again later.';
		$this->output( 'HTTP/1.1 503 Service Unavailable', 'cross' );
	}
}
