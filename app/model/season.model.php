<?php
/**
 * SeasonModel class.
 *
 * @author Manel Perez
 */

/**
 * Class SeasonModel.
 *
 * Core SeasonModel class.
 */
class SeasonModel extends Model
{
	/**
	 * Database instance.
	 *
	 * @var instance $database Database instance.
	 */
	protected $database;

	/**
	 * Initialize SeasonModel.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->database		= MysqlDatabase::getInstance( FUTBOL_DATABASE_NAME );
	}

	/**
	 * Return a match indentified by date.
	 *
	 * @return array
	 */
	public function getSeasonById( $id_season )
	{
		$id_season = intval( $id_season );

		$query = <<<QUERY
SELECT
	*
FROM
	season
WHERE
	id_season = '$id_season'
QUERY;

		return $this->database->query( $query, "Get season #$id_season" );
	}

	/**
	 * Return a match indentified by date.
	 *
	 * @return array
	 */
	public function getLastSeason()
	{
		$query = <<<QUERY
SELECT
	*
FROM
	season
ORDER BY
	id_season DESC
LIMIT 1
QUERY;

		return $this->database->query( $query, 'Get latest season' );
	}
}
