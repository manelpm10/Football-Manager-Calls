<?php
/**
 * Index file for the framework. Calls the dispatcher.
 *
 * @author Manel Perez
 */
error_reporting(0);
chdir( dirname( __FILE__ ) );

require_once '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'init.php';

$dispatcher = new Dispatcher;

$dispatcher->run();
