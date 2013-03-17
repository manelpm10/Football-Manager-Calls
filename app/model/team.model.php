<?php
/**
 * TeamModel class.
 *
 * @author Manel Perez
 */

/**
 * Class TeamModel.
 *
 * Core TeamModel class.
 */
class TeamModel extends Model
{
	/**
	 * Database instance.
	 *
	 * @var instance $database Database instance.
	 */
	protected $database;

	/**
	 * Initialize TeamModel.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->database		= MysqlDatabase::getInstance( FUTBOL_DATABASE_NAME );
	}

	/**
	 * Return information about all players.
	 *
	 * @param strint $type Filter players by type.
	 * @return array
	 */
	public function getPlayers( $type )
	{
		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	type = '$type'
QUERY;

		return $this->database->query( $query, 'Get all players for type #' . $type );
	}

	/**
	 * Return number of players for a type.
	 *
	 * @param string $type Type of player for count.
	 * @return integer
	 */
	public function getPlayersByType( $type = false )
	{
		$where = ( false !== $type )? "AND type = '$type'" : '';

		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	type != 'hidden'
	$where
QUERY;

		$result = $this->database->query( $query, 'Count the number of players for a type #' . $type );

		return $result;
	}
}
