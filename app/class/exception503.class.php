<?php
/**
 * Exception503 class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Exception503.
 *
 * Exception class that manages HTTP 503 error exceptions.
 */
class Exception503 extends CustomException
{
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
