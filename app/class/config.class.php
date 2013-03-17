<?php
/**
 * Config class.
 *
 * @author Carlos Soriano
 */

/**
 * Class Config.
 *
 * Core Controller class.
 */
class Config
{
	protected $data; //Stored ini array.

	/**
	 * Retrieves the data from the specified ini file.
	 *
	 * @param string $config_file Config file to retrieve data from.
	 * @param boolean $parseGroups Toggles parsing of ini groups.
	 * @return void
	 */
	public function start( $config_file = NULL, $parseGroups = true )
	{
		if(!$config_file) $config_file = DEFAULT_CONFIG_FILE;
		$this->data = parse_ini_file( $config_file, $parseGroups );
	}

	/**
	 * Retrieves a config variable.
	 *
	 * @param string $group Group of keys in the ini.
	 * @param string $key Key of the data to retrieve.
	 * @return mixed
	 */
	public function getConfig( $group = NULL, $key = NULL )
	{
		if ( !empty( $group ) )
		{
			if ( empty( $key ) )
			{
				if ( isset( $this->data[ $group ] ) )
				{
					return $this->data[ $group ];
				}
			}
			else
			{
				if ( isset( $this->data[ $group ][ $key ] ) )
				{
					return $this->data[ $group ][ $key ];
				}
			}
		}
		else
		{
			return $this->data;
		}
	}

	/**
	 * Returns the array of values of a php config file.
	 *
	 * Usage Config::getConfigFile( 'config_name', 'key_of_array' );
	 *
	 * @param string $config_filename Name of the config file to return.
	 * @param string $group [optional] Key of the config array.
	 * @return array
	 */
	public static function getConfigFile( $config_filename, $group = false )
	{
		$config_path = PATH_CONFIG . $config_filename . '.config.php';

		if ( is_readable( $config_path ) )
		{
			include $config_path;

			if ( !is_array( $config ) )
			{
				throw new Exception503( '"$config_filename" isn\'t a valid config file. It must contain a $config array.' );
			}

			if ( $group != false )
			{
				if ( !isset( $config[ $group ] ) )
				{
					throw new Exception503( "The specified config group '$group' doesn't exist!" );
				}
				return $config[ $group ];
			}

			return $config;
		}
		else
		{
			throw new Exception503( 'Can\'t open the "' . $config_filename . '" config file!' );
		}
	}
}
