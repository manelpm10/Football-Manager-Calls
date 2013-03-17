<?php
/**
 * MatchModel class.
 *
 * @author Manel Perez
 */

/**
 * Class MatchModel.
 *
 * Core MatchModel class.
 */
class MatchModel extends Model
{
	/**
	 * Database instance.
	 *
	 * @var instance $database Database instance.
	 */
	protected $database;

	/**
	 * Initialize MatchModel.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->database		= MysqlDatabase::getInstance( FUTBOL_DATABASE_NAME );
	}

	/**
	 * Return a match indentified by id.
	 *
	 * @return array
	 */
	public function getMatchById( $id_match )
	{
		$id_match = intval( $id_match );

		$query = <<<QUERY
SELECT
	*
FROM
	matches
WHERE
	id_match = '$id_match'
QUERY;

		return $this->database->query( $query, 'Get a match filtered by Id' );
	}

	/**
	 * Return a match indentified by date.
	 *
	 * @return array
	 */
	public function getMatchByDate( $date, $limit = false )
	{
		$date = $this->database->real_escape_string( $date );
		if ( false != $limit )
		{
			$limit = 'LIMIT ' . intval( $limit );
		}

		$query = <<<QUERY
SELECT
	*
FROM
	matches
WHERE
	day = '$date'
ORDER BY
	day
$limit
QUERY;

		return $this->database->query( $query, 'Get a match filtered by date' );
	}

	/**
	 * Return the next match from matches table.
	 *
	 * @return array
	 */
	public function getNextMatches( $id_season = false, $limit = false )
	{
		$filter = '';

		if ( false != $limit )
		{
			$limit = 'LIMIT ' . intval( $limit );
		}

		if ( false !== $id_season )
		{
			$filter = 'id_season = ' . intval( $id_season ) . ' AND';
		}

		$query = <<<QUERY
SELECT
	*
FROM
	matches
WHERE
	$filter
	day > NOW() AND
	status != 'hidden'
ORDER BY
	day
$limit
QUERY;

		return $this->database->query( $query, 'Get the next match' );
	}

	/**
	 * Return the last match from matches table.
	 *
	 * @return array
	 */
	public function getLastMatches( $id_season = false, $limit = false )
	{
		if ( false != $limit )
		{
			$limit = 'LIMIT ' . intval( $limit );
		}

		if ( false != $id_season )
		{
			$filter = 'id_season = ' . intval( $id_season ) . ' AND';
		}

		$query = <<<QUERY
SELECT
	*
FROM
	matches
WHERE
	$filter
	day < NOW() AND
	status != 'hidden'
ORDER BY
	day DESC
$limit
QUERY;

		return $this->database->query( $query, 'Get the last match' );
	}

	/**
	 * Return all the players for a id_match.
	 *
	 * @param integer $id_match Identifier of match.
	 * @return array
	 */
	public function getPlayersForMatch( $id_match, $available_state = false, $order_by = false, $exclude_player = false )
	{
		$id_match			= intval( $id_match );

		$available_filter	= '';
		if ( false !== $available_state )
		{
			foreach ( $available_state as $state )
			{
				$available_state	= $this->database->real_escape_string( $state );
				$available_filter[]	= "available = '$available_state'";
			}
			$available_filter		= '(' . implode( ' OR ', $available_filter ) . ') AND ';
		}

		$exclude_player_filter = ( false === $exclude_player )? '' : "p.id_player != $exclude_player AND ";
		$order_by = ( false === $order_by )? 'status_order, position' : $order_by;

		$query = <<<QUERY
SELECT
	mp.id_match,
	mp.available,
	p.id_player,
	p.name,
	p.position,
	p.email,
	p.sanitized_name,
	p.middle_name,
	ROUND( ( SELECT AVG( mp2.score ) FROM matches_player mp2 WHERE mp2.id_player = p.id_player AND mp2.available = 'called' GROUP BY mp2.id_player ), 2 )AS player_score,
	p.image_url,
	(
		CASE
			WHEN mp.available = 'called' THEN 1
			WHEN mp.available = 'available' THEN 2
			WHEN mp.available = 'if_necessary' THEN 3
			ELSE 4
		END
	) AS status_order
FROM
	matches_player mp
	INNER JOIN player p USING( id_player )
WHERE
	$exclude_player_filter
	$available_filter
	id_match = $id_match
ORDER BY
	$order_by
QUERY;

		if ( !$players = $this->database->query( $query, 'Get players for a match' ) )
		{
			return array();
		}

		$url = $this->getClass( 'Url' );
		foreach ( $players as &$player )
		{
			$player['player_url'] = $url->buildUrl( 'player', 'index', array( $player['sanitized_name'] ) );
		}
		return $players;
	}

	/**
	 * Return all the players for a id_match.
	 *
	 * @param integer $id_match Identifier of match.
	 * @return array
	 */
	public function getPlayersForMatchWithStatistics( $id_match, $id_season, $order_by = false )
	{
		$id_match	= intval( $id_match );
		$id_season	= intval( $id_season );
		$order_by	= ( false === $order_by )? 'status_order, p4.position, num_times_rotated DESC' : $order_by;

		$query = <<<QUERY
SELECT
	mp4.id_match,
	mp4.available,
	p4.id_player,
	p4.name,
	p4.position,
	p4.email,
	p4.sanitized_name,
	p4.middle_name,
	p4.image_url,
	(
		SELECT
			COUNT( mp1.id_match ) AS num
		FROM
			matches_player mp1
			INNER JOIN matches m2 USING ( id_match )
			INNER JOIN player p1 USING ( id_player )
		WHERE
			p1.id_player = p4.id_player AND
			m2.id_season = $id_season AND
			m2.status = 'closed' AND
			m2.type IN ( 'league', 'cup' ) AND
			m2.id_match != $id_match AND
			mp1.available = 'available'
	) AS num_times_rotated,
	(
		(
			1 * (
				SELECT
					COUNT( mp1.id_match ) AS num
				FROM
					matches_player mp1
					INNER JOIN matches m2 USING ( id_match )
					INNER JOIN player p1 USING ( id_player )
				WHERE
					p1.id_player = p4.id_player AND
					m2.id_season = $id_season AND
					m2.status = 'closed' AND
					m2.type IN ( 'league', 'cup' ) AND
					m2.id_match != $id_match AND
					mp1.available = 'available'
			)
		)
		- 
		(
			(
				0.25 * (
					(
						SELECT
							COUNT( m4.id_match ) AS num
						FROM
							matches m4
						WHERE
							m4.id_season = $id_season AND
							m4.id_match != $id_match AND
							m4.status = 'closed' AND
							m4.type IN ( 'league', 'cup' )
					) - (
						SELECT
							COUNT( mp3.id_match ) AS num
						FROM
							matches_player mp3
							INNER JOIN matches m5 USING ( id_match )
							INNER JOIN player p3 USING ( id_player )
						WHERE
							p3.id_player = p4.id_player AND
							m5.id_season = $id_season AND
							m5.status = 'closed' AND
							m5.type IN ( 'league', 'cup' ) AND
							m5.id_match != $id_match AND
							mp3.available IN ( 'available', 'injuried', 'called', 'if_necessary' )
					)
				)
			)
			+
			(
				1 * (
					SELECT
						COUNT( mp1.id_match ) AS num
					FROM
						matches_player mp1
						INNER JOIN matches m2 USING ( id_match )
						INNER JOIN player p1 USING ( id_player )
					WHERE
						p1.id_player = p4.id_player AND
						m2.id_season = $id_season AND
						m2.status = 'closed' AND
						m2.type IN ( 'league', 'cup' ) AND
						m2.id_match != $id_match AND
						mp1.available = 'missed'
				)
			)
		)
	) AS rotate_index,
	(
		CASE
			WHEN mp4.available = 'called' THEN 1
			WHEN mp4.available = 'available' THEN 2
			WHEN mp4.available = 'if_necessary' THEN 3
			ELSE 4
		END
	) AS status_order
FROM
	matches_player mp4
	INNER JOIN player p4 USING( id_player )
WHERE
	mp4.id_match = $id_match
ORDER BY
	$order_by
QUERY;

		if ( !$players = $this->database->query( $query, 'Get players for a match' ) )
		{
			return array();
		}

		$url = $this->getClass( 'Url' );
		foreach ( $players as &$player )
		{
			$player['player_url']	= $url->buildUrl( 'player', 'index', array( $player['sanitized_name'] ) );
		}

		return $players;
	}

	/**
	 * Return array with players that not has provided availability for a match
	 */
	public function getPlayersNotJoinedToMatch( $id_match, $match_type )
	{
		$id_match		= intval( $id_match );
		$player_type	= ( 'friendly' === $match_type )? 'p.type != "hidden"' : 'p.type = "player"';

		$query = <<<QUERY
SELECT
	p.*
FROM
	player p
	LEFT JOIN matches_player mp ON p.id_player = mp.id_player AND mp.id_match = $id_match
WHERE
	mp.id_player IS NULL  AND
	$player_type
QUERY;

		return $this->database->query( $query, "Get players that not has provided availability for match #$id_match" );
	}

	/**
	 * Check if a player have voted for a call.
	 *
	 * @param integer $id_match Identifier of match.
	 * @param integer $id_player Identifier of player.
	 * @return boolean
	 */
	public function isPlayerInMatch( $id_match, $id_player, $available_state = false )
	{
		$id_match	= intval( $id_match );
		$id_player	= intval( $id_player );

		$available_filter	= '';
		if ( false !== $available_state )
		{
			foreach ( $available_state as $state )
			{
				$available_state	= $this->database->real_escape_string( $state );
				$available_filter[]	= "available = '$available_state'";
			}
			$available_filter		= '(' . implode( ' OR ', $available_filter ) . ') AND ';
		}

		$query = <<<QUERY
SELECT
	COUNT( 1 ) AS num
FROM
	matches_player
WHERE
	$available_filter
	id_match = $id_match AND
	id_player = $id_player
QUERY;

		$result = $this->database->query( $query, 'Get players for a match' );

		return ( $result[0]['num'] > 0 ) ? true : false;
	}

	/**
	 * Join a player to a match with an available or unavailable state.
	 *
	 * @param integer $id_match Identifier of match.
	 * @param integer $id_player Identifier of player.
	 * @param string $avaialble Available type.
	 * @return boolean
	 */
	public function joinPlayerToMatch( $id_match, $id_player, $available )
	{
		$id_match	= intval( $id_match );
		$id_player	= intval( $id_player );
		$available	= $this->database->real_escape_string( $available );

		$query = <<<QUERY
INSERT INTO matches_player
	( id_match, id_player, available )
VALUES
	( $id_match, $id_player, '$available' )
ON DUPLICATE KEY UPDATE available = '$available';
QUERY;

		$this->database->query( $query, "Join player #$id_player to match #$id_match with status $available" );

		return true;
	}

	/**
	 * Close a match.
	 *
	 * @param integer $id_match Identifier of match.
	 * @return boolean
	 */
	public function closeMatch( $id_match )
	{
		$id_match	= intval( $id_match );

		$query = <<<QUERY
UPDATE
	matches
SET
	status = 'closed'
WHERE
	id_match = $id_match
QUERY;

		$this->database->query( $query, "Closing match #$id_match" );

		return true;
	}

	/**
	 * Set called status for players.
	 *
	 * @param integer $id_match Identifier of match.
	 * @param array $players Array with players to play.
	 * @return boolean
	 */
	public function setCalledPlayers( $id_match, $players )
	{
		$id_match	= intval( $id_match );
		$players	= implode( ",", $players );
		$players	= $this->database->real_escape_string( $players );

		$query = <<<QUERY
UPDATE
	`matches_player`
SET
	`available` = 'available'
WHERE
	id_match = $id_match AND
	`available` = 'called'
QUERY;

		$this->database->query( $query, "Set all called players to available status" );

		$query = <<<QUERY
UPDATE
	`matches_player`
SET
	`available` = 'called'
WHERE
	id_match = $id_match AND
	id_player IN ( $players )
QUERY;

		$this->database->query( $query, "Set called players" );

		return true;
	}

	/**
	 * Save a score for a players in a match.
	 *
	 * @param integer $id_match Identifier of match.
	 * @param integer $id_player_scorer Identifier of player scorer.
	 * @param array $score_info Array with score information.
	 */
	public function scoreMatchPlayers( $id_match, $id_player_scorer, $score_info )
	{
		$id_match 			= intval( $id_match );
		$id_player_scorer	= intval( $id_player_scorer );

		foreach ( $score_info as $score )
		{
			$id_player = intval( $score['id_player'] );
			$best	= $this->database->real_escape_string( $score['best'] );
			$worst	= $this->database->real_escape_string( $score['worst'] );

			$new_score = 'NULL';
			if ( '' != $score['score'] )
			{
				$new_score = "'{$score['score']}'";
				$query = <<<QUERY
UPDATE
	`matches_player`
SET
	`score` = IF( ( ISNULL( `score` ) ),
						$new_score,
						ROUND( ( ( `score` + $new_score ) / 2 ), 2 ) )
WHERE
	id_match = $id_match AND
	id_player = $id_player
LIMIT 1
QUERY;

				$this->database->query( $query, "Update average for player #$id_player and match #$id_match" );
			}

			$values[] = "( $id_match, $id_player, $id_player_scorer, $new_score, '$best', '$worst' )";
		}
		$values = implode( ',', $values );

		$query = <<<QUERY
INSERT INTO matches_player_score
	( id_match, id_player, id_player_scorer, score, best, worst )
VALUES
	$values
QUERY;

		$this->database->query( $query, "Save score from player #$id_player_scorer to match #$id_match" );

		return true;
	}

	/**
	 * Check if player has scored a match.
	 *
	 * @param integer $id_match Identifier of match.
	 * @param integer $id_player Identifier of player.
	 */
	public function hasPlayerScored( $id_match, $id_player_scorer )
	{
		$id_match			= intval( $id_match );
		$id_player_scorer	= intval( $id_player_scorer );

		$query = <<<QUERY
SELECT
	COUNT( 1 ) AS num
FROM
	matches_player_score
WHERE
	id_match = $id_match AND
	id_player_scorer = $id_player_scorer
LIMIT 1
QUERY;

		$result = $this->database->query( $query, 'Has player scored' );

		return ( $result[0]['num'] > 0 ) ? true : false;
	}

	/**
	 * Determine if the match is closed to join.
	 *
	 * @param string $status Match status.
	 * @param date $date The date for the match.
	 */
	public function isClosedMatch( $status, $date_match )
	{
		$date_match	= strtotime( $date_match );

		// Date - 9 hours.
		$deadline	= ( $date_match - ( 9 * 3600 ) );

		// Date for now.
		$now		= time();

		return ( 'closed' == $status || ( $now > $deadline ) );
	}
}
