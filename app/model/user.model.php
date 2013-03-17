<?php
/**
 * UserModel class.
 *
 * @author Manel Perez
 */

/**
 * Class UserModel.
 *
 * Core UserModel class.
 */
class UserModel extends Model
{
	/**
	 * Database instance.
	 *
	 * @var instance $database Database instance.
	 */
	protected $database;

	/**
	 * Initialize UserModel.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->database		= MysqlDatabase::getInstance( FUTBOL_DATABASE_NAME );
	}

	/**
	 * Return information about all players.
	 *
	 * @param strint $username Username.
	 * @return array
	 */
	public function getUserInformation( $username )
	{
		$username = $this->database->real_escape_string( $username );

		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	username = '$username'
QUERY;

		return $this->database->query( $query, 'Get user information by username #' . $username );
	}

	/**
	 * Return admins from players.
	 *
	 * @return array
	 */
	public function getAdmins()
	{
		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	role LIKE '%admin%'
QUERY;

		return $this->database->query( $query, 'Get admins from player table' );
	}

	/**
	 * Set last login date for a id_player.
	 *
	 * @param integer $id_player Identifier of player.
	 * @return array
	 */
	public function setLastLoginDate( $id_player )
	{
		$id_player = intval( $id_player );
		$query = <<<QUERY
UPDATE
	player
SET
	last_login = NOW()
WHERE
	id_player = $id_player
QUERY;

		return $this->database->query( $query, "Set last login date for player #$id_player" );
	}
}
