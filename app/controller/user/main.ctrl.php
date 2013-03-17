<?php
/**
 * Template class.
 *
 * @author Manel Perez
 */

/**
 * Very simple login class.
 */
class MainUserController extends Controller
{
	/**
	 * Does the parent construct things an also creates a session object
	 * for its use in this controller.
	 *
	 * @return
	 */
	public function __construct()
	{
		parent::__construct();
		$this->session = Factory::getInstance( 'Session' );
	}

	public function run()
	{
		$this->login();
	}

	/**
	 * Check if user is logged.
	 *
	 * return boolean.
	 */
	public function isLogged()
	{
		if ( $this->session->exists( 'is_logged' ) && $this->session->get( 'is_logged' ) === true )
		{
			return true;
		}

		$target_url = trim( HTTP_HOST, '/' ) . $_SERVER['REQUEST_URI'];
		$this->session->set( 'target_url', $target_url );

		$this->redirectToLogin();
	}

	/**
	 * Check if user is an admin.
	 *
	 * return boolean.
	 */
	public function isAdmin()
	{
		return ( true === $this->session->get( 'is_logged' ) && false !== strpos( $this->session->get( 'role' ), 'admin' ) );
	}

	/**
	 * Check if user is a player.
	 *
	 * return boolean.
	 */
	public function isPlayer()
	{
		return ( true === $this->session->get( 'is_logged' ) && 'player' == $this->session->get( 'type' ) );
	}

	/**
	 * Login a user in system.
	 *
	 * @param string $username
	 * @param string $password
	 */
	protected function login()
	{
		$post		= Factory::getInstance( 'FilterPost' );
		$url		= Factory::getInstance( 'Url' );

		if ( $post->exists( 'submit_login' ) )
		{
			$username = $post->getText( 'username' );
			$password = $post->getText( 'password' );

			$method_name = 'isLogin' . ucfirst( LOGIN_TYPE );
			if ( !method_exists( $this, $method_name) )
			{
				throw new Exception( 'Login type "' . LOGIN_TYPE .'" not exists in UserMainController' );
			}

			if ( $this->$method_name( $username, $password ) )
			{
				$userinfo = $this->getData( 'UserModel', 'getUserInformation', array( 'username' => $username ) );
				$this->getData( 'UserModel', 'setLastLoginDate', array( 'id_player' => $userinfo[0]['id_player'] ) );

				$this->session->set( 'is_logged', true );
				if ( isset( $userinfo[0] ) )
				{
					$this->session->set( 'id_player', $userinfo[0]['id_player'] );
					$this->session->set( 'username', $userinfo[0]['username'] );
					$this->session->set( 'name', $userinfo[0]['name'] );
					$this->session->set( 'middle_name', $userinfo[0]['middle_name'] );
					$this->session->set( 'last_name', $userinfo[0]['last_name'] );
					$this->session->set( 'type', $userinfo[0]['type'] );
					$this->session->set( 'email', $userinfo[0]['email'] );
					$this->session->set( 'role', $userinfo[0]['role'] );
				}
				else
				{
					$this->session->set( 'role', 'guest' );
				}

				$location = $url->buildUrl( 'Match', 'Index' );
				if ( $this->session->exists( 'target_url' ) )
				{
					$location = $this->session->get( 'target_url' );
					$this->session->remove( 'target_url' );
				}

				header( "Location: $location" );
				die();
			}
			else
			{
				$this->template->assign( 'error', true );
				$this->displayLogin();
			}
		}
		else
		{
			$target_url = $this->session->get( 'target_url' );
			$this->template->assign( 'user', array( 'is_logged' => false, 'role' => 'guest' ) );
			$this->displayLogin();
		}
	}

	/**
	 * Check login for type Ldap.
	 *
	 * @param string $username Username.
	 * @param string $password Password.
	 * @return boolean
	 */
	protected function isLoginLdap( $username, $password )
	{
		$ldap = new Ldap();
		$ldap->init();
		return $ldap->login( $username, $password );
	}

	/**
	 * Check login for type Ldap.
	 *
	 * @param string $username Username.
	 * @param string $password Password.
	 * @return boolean
	 */
	protected function isLoginDb( $username, $password )
	{
		$userinfo = $this->getData( 'UserModel', 'getUserInformation', array( 'username' => $username ) );
		return ( !empty( $userinfo ) && $userinfo[0]['password'] == md5( $password ) );
	}

	/**
	 * Unset session variables.
	 */
	protected function logout()
	{
		$this->session->remove( 'is_logged' );
	}

	/**
	 * Display login form.
	 */
	protected function displayLogin()
	{
		$this->logout();
		$this->template->setLayout( 'layout' );
		$this->template->setTemplate( 'user/login' );
	}

	/**
	 * Redirect to login.
	 */
	protected function redirectToLogin()
	{
		$url = Factory::getInstance( 'Url' );

		$location = $url->buildUrl( 'User', 'Main' );
		header( "Location: $location" );
		die();
	}
}