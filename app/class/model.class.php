<?php
/**
 * Model class.
 *
 * @author Manel Perez
 */

/**
 * Class Model.
 *
 * Core Model class.
 */
abstract class Model
{
	/**
	 * Stores data of the model.
	 */
	protected $data;

	/**
	 * Cache object.
	 */
	public $cache;

	/**
	 * Saves the data when constructing the model.
	 *
	 * @return void
	 */
	public function __construct ()
	{
		$this->cache	= new Cache();
		if ( ENVIRONMENT == 'DEV' )
		{
			$this->cache->set_status( false );
		}
	}

	/**
	 * Returns a singleton class.
	 *
	 * @param string $classname Class name to instantiate.
	 * @return object
	 */
	protected function getClass( $classname )
	{
		return Factory::getInstance( $classname );
	}
}
