<?php
/**
 * PlayerModel class.
 *
 * @author Manel Perez
 */

/**
 * Class PlayerModel.
 *
 * Core PlayerModel class.
 */
class PlayerModel extends Model
{
	/**
	 * Database instance.
	 *
	 * @var instance $database Database instance.
	 */
	protected $database;

	/**
	 * Initialize PlayerModel.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->database		= MysqlDatabase::getInstance( FUTBOL_DATABASE_NAME );
	}

	/**
	 * Return information about one players filtered by sanitized name.
	 *
	 * @param strint $sanitized_name Sanitized name for the player.
	 * @return array
	 */
	public function getPlayerBySanitizedName( $sanitized_name )
	{
		$sanitized_name	= $this->database->real_escape_string( $sanitized_name );

		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	sanitized_name = '$sanitized_name'
QUERY;

		if ( !$players = $this->database->query( $query, 'Get player when sanitized name #' . $sanitized_name ) )
		{
			return array();
		}

		return $this->prepareResults( $players );
	}

	/**
	 * Return information about one players filtered by id.
	 *
	 * @param mixed $id_player Identifier of player or array with identifier of players.
	 * @return array
	 */
	public function getPlayerById( $id_player )
	{
		if ( is_array( $id_player ) )
		{
			$player_filter = 'id_player IN (' . implode( ',', $id_player ) . ')';
		}
		else
		{
			$player_filter = 'id_player = ' . intval( $id_player );
		}

		$query = <<<QUERY
SELECT
	*
FROM
	player
WHERE
	$player_filter
QUERY;

		if ( !$players = $this->database->query( $query, 'Get player by id_player #' . $id_player ) )
		{
			return array();
		}

		return $this->prepareResults( $players );
	}

	/**
	 * Prepare results.
	 *
	 * @param array $players Array with players.
	 * @return array
	 */
	protected function prepareResults( $players )
	{
		$url = $this->getClass( 'Url' );
		foreach ( $players as &$player )
		{
			$player['player_url'] = $url->buildUrl( 'player', 'index', array( $player['sanitized_name'] ) );
		}

		return $players;
	}
}
