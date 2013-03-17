<?php
/**
 * Exception404 class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Exception 404.
 *
 * Exception class that manages HTTP 403 error exceptions.
 */
class Exception404 extends CustomException
{
	/**
	 * Runs exception treatment.
	 * 
	 * @return void
	 */
	public function launch()
	{
		$this->logException();
		$this->message = 'The page does not exist.';
		$this->output( 'HTTP/1.1 404 Not Found', 'warning' );
	}
}
