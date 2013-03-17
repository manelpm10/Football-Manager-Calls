<?php

/**
 * Manage sendCall script.
 *
 * @author Manel Perez
 */
chdir  ( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
require_once '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'init.php';

/**
 * Script for send call to the player for next match.
 */
class SendCallScript extends Cron
{
	/**
	 * Run the concrete Script.
	 */
	public function run()
	{
		$send_call 	= new SendcallScriptController();

		// Launch the script.
		$send_call->execute();
	}
}

try
{
	$sc = new SendCallScript();
	$sc->run();
}
catch ( Exception $e)
{
	// Send an email.
	echo $e->getMessage() . "\n";
}
