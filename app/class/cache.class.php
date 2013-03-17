<?php

/**
 * Class that implements a simple cache system.
 *
 * @author Manel Perez
 */
class Cache
{
	/**
	 * Path to cache dir.
	 *
	 */
	const CACHE_DIR = PATH_CACHE;

	/**
	 * Max. time to life of this cache.
	 *
	 */
	const CACHE_TTL = '3600';

	/**
	 * Extension to use in cache files.
	 *
	 */
	const CACHE_EXT = '.cache';

	/**
	 * Extension to use in cache files.
	 *
	 */
	const DEFAULT_GROUP = 'default';

	/**
	 * Indicates whether the cache is enabled.
	 *
	 * @var boolean.
	 */
	private $caching = true;

	/**
	 * Contain the group path.
	 *
	 * @var string.
	 */
	private $group_path = '';

	/**
	 * Contain the file path.
	 *
	 * @var string.
	 */
	private $file_path = '';

	/**
	 * Contain only the filename.
	 *
	 * @var string.
	 */
	private $file_name = '';

	/**
	 * Contain the filename with path.
	 *
	 * @var string.
	 */
	private $file = '';

	/**
	 * Constructor of the class.
	 *
	 */
	public function __construct () {}

	/**
	 * This function returns the caching status (enabled?).
	 *
	 * @return boolean.
	 */
	public function get_status ()
	{
		return $this->caching;
	}

	/**
	 * Function that set a new cache status.
	 *
	 * @param boolean $status Enable or disable the cache.
	 * @return null.
	 */ 
	public function set_status ( $status )
	{
		$this->caching = $status;
	}

	/**
	 * Function that returns the path to cache dir.
	 * 
	 * @param string $id Identificator of the data.
	 * @param string $group_id Identificator of the group.
	 * @return string.
	 */
	private function setFileRoutes ( $id, $group_id )
	{
		$key_name	= $this->getKey ( $id );
		$key_group	= $this->getKey ( $group_id );

		$level_one = $key_group;
		$level_two = substr ( $key_name, 0, 4 );

		$this->group_path	= self::CACHE_DIR . $level_one . DIRECTORY_SEPARATOR;
		$this->file_path	= self::CACHE_DIR . $level_one . DIRECTORY_SEPARATOR . $level_two . DIRECTORY_SEPARATOR;
		$this->file_name	= $key_name . self::CACHE_EXT;
		$this->file			= $this->file_path . $this->file_name;
	}

	/**
	 * Function that returns a hashed key name from an id.
	 * 
	 * @param string $id Identificator of the data.
	 * @return hash.
	 */
	private function getKey ( $id )
	{
		return md5 ( $id );
	}
	
	/**
	 * Function that check the correct integrity between the rescue hash from the cache and the 
	 * generated hash.
	 * 
	 * @param hash $read_hash Contains the rescue hash from the cache file.
	 * @param string $seralized_data Data to make the local hash to check te integrity.
	 * @return boolean.
	 */
	private function checkIntegrity ( $read_hash, $serialized_data )
	{
		$hash = md5 ( $serialized_data );

		return ( $read_hash == $hash );
	}

	/**
	 * Function that check the expiration time.
	 * 
	 * @param timestamp $expiration_time Contain the rescue time of expiration from the cache file.
	 * @return boolean.
	 */
	private function checkExpiration ( $expiration_time )
	{
		return ( time() < $expiration_time );
	}

	/**
	 * Function that save a data into cache system.
	 *
	 * @param string $id Identificator of the data.
	 * @param string $group_id Identificator of the group.
	 * @param array $data The data to be cached.
	 * @param timestamp $ttl The time to expires.
	 * @return mixed int or boolean. Returns the number of bytes that were written to the file, or FALSE on failure. 
	 */
	public function save ( $id, $group_id = self::DEFAULT_GROUP, $data, $ttl = self::CACHE_TTL )
	{
		if ( $this->caching )
		{
			$group_id = ( empty( $group_id ) )? self::DEFAULT_GROUP: $group_id;
			$this->setFileRoutes( $id, $group_id );

			// If the directory don't exists, I create.
			if ( !is_dir ( $this->file_path ) )
			{
				if ( !mkdir ( $this->file_path, 0777, true ) )
				{
					return false;
				}

				// Force chmod.
				chmod( $this->file_path, 0777 );
			}

			$single_value = 0;
			if ( !is_array ( $data ) && !is_object( $data ) )
			{
				$data			= array ( $data );
				$single_value	= 1;
			}

			// Serialize data for caching.
			$data = serialize ( $data );

			// I get a hash to check integrity in the future.
			$hash = md5 ( $data );

			$meta['expiration_time']	= time () + $ttl;
			$meta['integrity']			= $hash;
			$meta['single_value']		= $single_value;
			$meta['data']				= $data;

			// Serialize meta info to put in a file.
			$data	= serialize ( $meta );

			if( false !== file_put_contents ( $this->file, $data, LOCK_EX ) )
			{
				chmod( $this->file, 0777 );
				return true;
			}
		}

		return false;
	}

	/**
	 * If the cache is enabled, the integrity of file is ok and the file is not expired, return the cached file.
	 *
	 * @param string $id Identificator of the data.
	 * @param string $group_id Identificator of the group.
	 * @return mixed String or boolean values.
	 */
	public function get ( $id, $group_id = self::DEFAULT_GROUP )
	{
		if ( $this->caching )
		{
			$this->setFileRoutes ( $id, $group_id );

			if ( !file_exists ( $this->file ) )
			{
				return false;
			}

			$meta	= file_get_contents ( $this->file_path . $this->file_name );
			$meta	= unserialize ( $meta );

			$check_expiration	= $this->checkExpiration ( $meta['expiration_time'] );
			$check_integrity	= $this->checkIntegrity ( $meta['integrity'], $meta['data'] );

			// Expiration and integrity control.
			if ( $check_expiration && $check_integrity )
			{
				$data = unserialize ( $meta['data'] );

				if ( $meta['single_value'] == 1 )
				{
					$data	= $data[0];
				}

				return $data;
			}
			else
			// Clean the expired or not correct cache.
			{
				$this->remove ( $id, $group_id );

				return false;
			}
		}

		return false;
	}

	/**
	 * Remove cache and meta file identificated by id. If group_level is true, clean all group cache.
	 *
	 * @param string $id Identificator of the data.
	 * @param string $group_id Identificator of the group.
	 * @param boolean $group_level True if I wish delete all cache group. False by default.
	 * @return boolean.
	 */
	public function remove( $id, $group_id = self::DEFAULT_GROUP, $group_level = false )
	{
		$this->setFileRoutes( $id, $group_id );

		// Don't remove the DEFAULT_GROUP group
		if ( $group_level && $group_id != self::DEFAULT_GROUP )
		{
			if ( !$this->group_path || empty ( $this->group_path ) )
			{
				return false;
			}

			return $this->delTree ( $this->group_path );
		}
		// Check that the file exists and delete the file cached folder
		elseif ( $this->file_path && !empty ( $this->file_path ) )
		{
			return $this->delTree ( $this->file_path );
		}
		else
		{
			return false;
		}
	}

	/**
	 * Remove all cached items.
	 */
	public function clearAllCache()
	{
		foreach ( scandir ( self::CACHE_DIR ) as $dir )
		{
			if ( $dir == '.' || $dir == '..' )
			{
				continue;
			}

			$this->delTree ( self::CACHE_DIR . $dir . DIRECTORY_SEPARATOR );
		}
	}

	/**
	 * Delete a dir and all his content.
	 * 
	 * @param string $dir Path to the dir to be recursive deleted.
	 * @return boolean .
	 */
	private function delTree( $dir )
	{
		if ( empty ( $dir ) || strpos( $dir, self::CACHE_DIR ) != 0 )
		{
			return false;
		}

		if ( !file_exists ( $dir ) )
		{
			return true;
		}

		if ( !is_dir ( $dir ) || is_link ( $dir ) )
		{
			return unlink ( $dir ); 
		}

		foreach ( scandir ( $dir ) as $item )
		{
			if ( $item == '.' || $item == '..' )
			{
				continue;
			}

			if( is_dir ( $dir . DIRECTORY_SEPARATOR . $item ) )
			{
				$this->delTree ( $dir . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR );
			}
			else
			{
				unlink ( $dir . DIRECTORY_SEPARATOR . $item );
			}
		}

		return rmdir ( $dir );
	}
}
