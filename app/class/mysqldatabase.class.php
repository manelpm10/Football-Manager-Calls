<?php
/**
 * Database class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Database.
 *
 * Core Database class.
 */
class MysqlDatabase extends Mysqli implements DatabaseInterface
{
	/**
	 * @var object $instance Contains instance of database object.
	 */
	private static $instance;

	/**
	 * @var object $stmt Statement object for prepared statements.
	 */
	protected $stmt;

	/**
	 * @var array $query_log Stores all the queries done in the session.
	 */
	protected $query_log;

	/**
	 * Gets a singleton object from the database class.
	 *
	 * @param string $profile Name of the profile configuration to load.
	 * @return bool Returns true if everything is ok, false otherwise.
	 */
	public static function getInstance( $profile )
	{
		if ( empty( self::$instance[ $profile ] ) )
		{
			$class = get_class();
			self::$instance[ $profile ] = new $class( $profile );
		}

		return self::$instance[ $profile ];
	}

	/**
	 * Construct method for initializing methods and variables.
	 *
	 * @param string $profile Name of the profile configuration to load.
	 */
	public function __construct( $profile )
	{
		$this->query_log = array();

		// Retrieve database config.
		include( PATH_CONFIG . 'database.config.php' );

		// Choose the configuration for $profiale and environment.
		$config = $config[$profile][ENVIRONMENT];

		$port	= isset( $config['port'] ) ? $config['port'] : null;

		// Try to connect to database.
		parent::__construct( $config['host'], $config['user'], $config['pass'], $config['dbname'], $port );
		parent::set_charset( $config['charset'] );

		// Throw exception if there was a problem connecting.
		if ( $error = mysqli_connect_error() )
		{
			throw new MysqlDatabaseException( 'Could not connect to database: ' . mysqli_connect_errno() . '-' . $error );
		}

		mysqli_report( MYSQLI_REPORT_ERROR );
	}

	/**
	 * Makes a query to the database, and if it returns a resultset, fetches it and returns an array.
	 *
	 * @param string $sql Sql statement to query.
	 * @param string $context Small description of the query.
	 * @param boolean $fetch_one Return only one result.
	 * @return mixed Returns true if no resulset was returned from the query.
	 * If a resultset was returned, returns the fetched resultset array. If
	 * something went wrong, returns false.
	 */
	public function query( $sql, $context, $fetch_one = false )
	{
		$result_set = parent::query( $sql );

		$error = null;

		if ( stripos( $sql, 'SELECT' ) !== false && stripos( $sql, 'SELECT' ) < 10 )
		{
			if ( $result_set instanceof mysqli_result )
			{
				if ( $fetch_one )
				{
					$result = $result_set->fetch_assoc();
				}
				else
				{
					while ( $array = $result_set->fetch_assoc() )
					{
						$result[] = $array;
					}
				}
				mysqli_free_result( $result_set );
			}
			else
			{
				$error = $this->error;
				throw new MysqlDatabaseException( 'Query ' . $context . ' failed: ' . $this->error );
			}
		}

		/*
		$this->query_log += array (
				'sql' 		=> $sql,
				'context'	=> $context,
				'error'		=> $error
		);*/

		if ( empty( $result ) )
		{
			return;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Prepares a sql statement.
	 *
	 * @param string $sql Sql query.
	 * @return void
	 */
	public function prepare( $sql )
	{
		$this->stmt = $this->stmt_init();

		$this->stmt->prepare( $sql );
	}

	/**
	 * Binds parameters to a prepared statement query.
	 *
	 * @return void
	 */
	public function bindParam()
	{
		$args = func_get_args();

		call_user_func_array( array( &$this->stmt, 'bind_param' ), $args );
	}

	/**
	 * Executes the prepared statement.
	 *
	 * @return boolean
	 */
	public function execute()
	{
		$feedback = $this->stmt->execute();

		if ( !$feedback )
		{
			throw new Exception503( 'Query failed: ' . $this->stmt->error );
		}

		return $feedback;
	}

	/**
	 * Closes the current prepared statement.
	 *
	 * @return boolean
	 */
	public function stmtClose()
	{
		return $this->stmt->close();
	}

	/**
	 * Binds results to the given variables.
	 *
	 * @return void
	 */
	public function bindResult()
	{
		throw new Exception503( 'We must fix the passing of undefinied variables to this function before we can use it.' );

		$args = func_get_args();

		call_user_func_array( array( &$this->stmt, 'bind_result' ), $args );
	}

	/**
	 * Fetch results from a prepared statement into the bound variables.
	 *
	 * @return bool
	 */
	public function fetch()
	{
		throw new Exception503( 'We must fix the passing of undefinied variables to bindResult function before we can use it.' );

		return $this->stmt->fetch();
	}

	/**
	 * Sets the client connection charset.
	 *
	 * @param string $charset Charset.
	 * @return bool Returns true if everything went ok, false otherwise.
	 */
	public function setCharset( $charset )
	{
		parent::set_charset( $charset );
	}

	/**
	 * Ends the current database connection.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		parent::close();
	}
}

/**
 * MysqlDatabaseException class
 *
 * Database Exception class for the MysqlDatabase class
 */
class MysqlDatabaseException extends CustomException
{

}
