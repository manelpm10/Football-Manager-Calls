<?php
/**
 * Exception403 class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Exception 403.
 *
 * Exception class that manages HTTP 403 error exceptions.
 */
class Exception403 extends CustomException
{
	/**
	 * Runs exception treatment.
	 * 
	 * @return void
	 */
	public function launch()
	{
		$this->logException();
		$this->output( 'HTTP/1.1 403 Forbidden', 'forbidden' );
	}
}
