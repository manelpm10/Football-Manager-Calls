<?php
/**
 * Class description.
 */

/**
 * Class to interact with the Active Directory.
 *
 */
class Ldap
{
	/**
	 * Link to the Active Directory server.
	 *
	 * @var Resource
	 */
	var $active_directory_connection = null;

	/**
	 * Bind to the Active Directory server.
	 *
	 * @var Resource
	 */
	var $active_directory_bind = null;

	/**
	 * Error code.
	 *
	 * @var string
	 */
	var $error = null;

	/**
	 * Error number.
	 *
	 * @var integer
	 */
	var $error_number = null;

	/**
	 * User to log in in the Active Directory server.
	 *
	 * @var string
	 */
	var $active_directory_login;

	/**
	 * Password to log in the Active Directory server, encoded in base64.
	 *
	 * @var string
	 */
	var $active_directory_password;

	/**
	 * Server name of the Active Directory.
	 *
	 * @var string
	 */
	var $active_directory_server;

	/**
	 * Postfix of Ldap server.
	 *
	 * @var string
	 */
	var $active_directory_user_postfix;

	/**
	 * Single instance.
	 *
	 * @var object
	 */
	var $instance;

	/**
	 * Needed fields from the Active Directory, to avoid the rest of fields from the Ldap result.
	 *
	 * @var array
	 */
	var $user_info_required;

	/**
	 * Path where the users are in the Active Directory.
	 *
	 * @var string
	 */
	var $user_search_path;

	/**
	 * Connects to the Active Directory Server.
	 *
	 * @param string $profile Profile.
	 * @param Config $conf Config object.
	 */
	function init()
	{
		// Connect to the Active Directory. This credentials should be implemented in a config file.
		if( !isset( $this->active_directoy_connection ) )
		{
			if ( !function_exists( 'ldap_connect' ) )
			{
				die( 'This server appears to be missing LDAP support' );
			}
			//include_once( PATH_CONFIG	. 'ldap.config.php' );
			$config = Config::getConfigFile( 'ldap' );
			$this->active_directory_login = $config['active_directory_login'];
			$this->active_directory_password = $config['active_directory_base64_password'];
			$this->active_directory_user_postfix = $config['active_directory_user_postfix'];
			$this->active_directory_server = $config['active_directory_server'];
			$this->user_search_path = $config['user_search_path'];
			$this->user_info_required = split( ',', $config['user_info_required_fields'] );

			// Set the connection.
			$this->active_directory_connection = ldap_connect( $this->active_directory_server );

			// Some needed options.
			ldap_set_option( $this->active_directory_connection, LDAP_OPT_PROTOCOL_VERSION, 3 );
			ldap_set_option( $this->active_directory_connection, LDAP_OPT_REFERRALS, 0 ) ;

			// Log in the server.
			$this->active_directory_bind = ldap_bind(
				$this->active_directory_connection,
				$this->active_directory_login . $this->active_directory_user_postfix,
				base64_decode( $this->active_directory_password )
			);

			if( !$this->active_directory_bind )
			{
				die( 'Could not connect to the Active Directory server
					' . $this->active_directory_server . ' with user ' . $this->active_directory_login . $this->active_directory_user_postfix );
			}
		}
	} // end __construct.

	/**
	 * Verifies if the credentials are correct.
	 *
	 * @access public
	 * @param string $username User nick.
	 * @param string $password User password.
	 * @return boolean
	 */
	function login( $username, $password )
	{
		$username .= $this->active_directory_user_postfix;
		/**
		 * Catch the exception if the login is not correct, otherwise return true.
		 */
		if ( @ldap_bind( $this->active_directory_connection, $username, $password ) && $password != "" )
		{
			return true;
		}
		else
		{
			$this->error = ldap_error( $this->active_directory_connection );
			return false;
		}
	} // end login.

	/**
	 * Search for users in the Active Directory.
	 *
	 * @param string $name Some characters of the user to search.
	 * @param boolean $only_actives Flag to search only the active users.
	 * @return array
	 */
	function searchUser( $name = "", $only_actives = false )
	{
		// Needs to bind again.
			$this->active_directory_bind = ldap_bind(
			$this->active_directory_connection,
			$this->active_directory_login . $this->active_directory_user_postfix,
			base64_decode( $this->active_directory_password ) );

		/**
		 * Filter string:
		 *  - samaccountname: usually name.surname
		 *  - displayname: Name Surname
		 */
		$search_filter = "(&(|(samaccountname=$name*)(displayname=$name*))(useraccountcontrol=" . ($only_actives ? 66048 : '*' ) . "))";

		$search = ldap_search(
			$this->active_directory_connection,
			$this->user_search_path,
			$search_filter,
			$this->user_info_required );

		if ( !$search )
		{
			die( 'Can\'t search: '.ldap_error( $this->active_directory_connection ) );
		}

		$search_results = ldap_get_entries( $this->active_directory_connection, $search );

		// Extract the total results.
		$total_results = $search_results['count'];
		unset( $search_results['count'] );

		// Process the array of results to put it in a better format.
		$i = 0;
		$results = array();
		if( is_array( $search_results ) )
		{
			foreach( $search_results AS $row )
			{
				foreach( $row AS $field => $value )
				{
					if( is_string( $field ) )
					{
						// Transform the date.
						if( $field == 'whencreated' )
						{
							// $value[0] = $this->_transformDate( $value[0] ); !
						}
						if( $field == 'memberof' )
						{
							foreach( $value as $key => $group )
							{
								preg_match('/^CN=([^,]+),(.*)$/',$group,$matches);
								if( count($matches) > 2 )
								{
									$results[$i]['memberof'][$matches[1]]=$matches[2];
								}
							}
						}
						else
						{
							$results[$i][$field] = $value[0];
						}
					}
				}
				$i++;
			}
		}
		return $results;
	}

	/**
	 * Transforms the "weird" Active Directory date format into timestamp.
	 *
	 * @param string $date The date in the Active Directory format, YYYYMMDDHHiiss.0Z .
	 */
	function _transformDate( $date )
	{
		$date = explode( '.', $date );
		$date = $date[0];
		$date = strtotime( $date );
		return $date;
	}

	/**
	 * Get the user for testing purposes.
	 *
	 * @return string
	 */
	 function getTestingUser()
	 {
	 	return $this->active_directory_login;
	 }

	 /**
	 * Get the password for testing purposes.
	 *
	 * @return string
	 */
	 function getTestingPassword()
	 {
	 	return base64_decode( $this->active_directory_password );
	 }
}
