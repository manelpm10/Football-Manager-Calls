<?php
/**
 * Database Interface
 * 
 * @author Carlos Soriano
 */

/**
 * Interface Database
 * 
 * Interface for database connection.
 */

interface DatabaseInterface
{	
	/**
	 * Gets a singleton object from the database class.
	 * 
	 * @param string $profile Name of the profile configuration to load.
	 * @return bool Returns true if everything is ok, false otherwise.
	 */
	public static function getInstance( $profile );
	
	/**
	 * Construct method for initializing methods and variables.
	 * 
	 * @param string $profile Name of the profile configuration to load.
	 * @return void
	 */
	public function __construct( $profile );
	
	/**
	 * Makes a query to the database, and if it returns a resultset, fetches
	 * it and returns an array.
	 * 
	 * @param string $sql Sql statement to query.
	 * @param string $context Small description of the query.
	 * @return mixed Returns true if no resulset was returned from the query.
	 * If a resultset was returned, returns the fetched resultset array. If
	 * something went wrong, returns false.
	 */
	public function query( $sql, $context );
	
	/**
	 * Sets the client connection charset.
	 * 
	 * @param string $charset Charset.
	 * @return bool Returns true if everything went ok, false otherwise.
	 */
	public function setCharset( $charset );
	
	/**
	 * Ends the current database connection
	 * 
	 * @return void
	 */
	public function __destruct();
}
