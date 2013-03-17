<?php
/**
 * I18n class.
 *
 * @author Manel Perez
 */

/**
 * Class I18n.
 *
 * Core I18n class.
 */
class I18n
{
	/**
	 * Instance of session.
	 *
	 * @var session
	 */
	private $session;
	
	/**
	 * Instance of cookie.
	 *
	 * @var cookie
	 */
	private $cookie;
	
	/**
	 * Gets the request url as default.
	 * 
	 * @return null
	 */
	public function __construct()
	{
		$this->session	= Factory::getInstance( 'Session' );
		$this->cookie	= Factory::getInstance( 'Cookie' );
		
		$this->setLocale();
	}
	/**
	 * This function set a locale. If I send the locale var, set this value, otherwise, search the best
	 * option.
	 *
	 * @param String $locale Identification of Languages in format xx_XX (RFC3066).
	 * @param String $domain The name of messages files.
	 * @param String $codeset The charset of the read file (UTF-8 by default).
	 */
	public function setLocale( $locale = null, $domain = I18N_DEFAULT_DOMAIN, $codeset = I18N_DEFAULT_CODESET )
	{
		if( !isset( $locale ) )
		{
			$locale = $this->determineLocale();
		}
		
		// Set the session and cookie locale for this user
		$this->setLocaleSession( $locale );
		
		// Asign vars to show the correct text language
		setlocale( LC_MESSAGES, $locale );
		bindtextdomain( $domain, PATH_I18N );
		textdomain( $domain );
		bind_textdomain_codeset( $domain, $codeset );
	}
	
	/**
	 * Search in current session or cookie or language browser. As last option, set the default locale.
	 *
	 * @return String locale.
	 */
	protected function determineLocale()
	{
		$session_locale	= $this->session->get( 'locale' );
		$cookie_locale	= $this->cookie->get( 'locale' );
		
		// Current session language
		if( !empty( $session_locale ) )
		{
			$locale = $session_locale;
		}
		// Search a cookie language
		elseif( !empty( $cookie_locale ) )
		{
			$locale = $cookie_locale;
		}
		// If don't have cookie, search the browser language and set cookie
		else
		{
			$locale = $this->getBrowserLanguage();
		}
		
		// If don't have cookie or browser language, set default locale
		if( !isset( $locale ) )
		{
			$locale = I18N_DEFAULT_LOCALE;
		}
		
		return $locale;
	}
	
	/**
	 * This function obtains the language of the browser and return this if it match with any 
	 * application languages.
	 * 
	 * @return mixed False or string with the browser language existing in the application. 
	 */
	protected function getBrowserLanguage()
	{
		if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )
		{
			// break up string into pieces (languages and q factors)
			preg_match_all( '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse );
			
			if ( count( $lang_parse[1] ) )
			{
				// create a list like "en" => 0.8
				$langs = array_combine( $lang_parse[1], $lang_parse[4] );
				
				foreach ( $langs as $lang => $val )
				{
					$lang = explode( '-', $lang );
					
					if( empty( $lang[1] ) )
					{
						continue;
					}
					else
					{
						$lang = strtolower( $lang[0] ) . '_' . strtoupper( $lang[1] );
					}
					
					if( file_exists( PATH_I18N . $lang . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . I18N_DEFAULT_DOMAIN . '.mo' ) )
					{
						$locale = $lang;
						
						return $locale;
					}
				}
			}
		}
		
		return;
	}
	
	/**
	 * Get an string with locale and set a cookie and current session at this.
	 *
	 * @param String $locale Name of the locale to set.
	 * @return null
	 */
	protected function setLocaleSession( $locale )
	{
		$session_locale = $this->session->get( 'locale' );
		
		// Only set the session information if the actual session locale is different of locale
		if( $locale != $session_locale )
		{
			// One month of life
			$expire =  ( 60 * 60 * 24 * 30 );
			$this->session->remove('locale');
			$this->session->set('locale', $locale);
			$this->cookie->set( 'locale', $locale, $expire );
		}
	}
	
	/**
	 * Temporal function to determine the RFC3066 lang. 
	 *
	 * @param String $lang
	 */
	public function getNormalizedLang( $lang )
	{
		switch( $lang )
		{
			case 'en':
				$lang = 'en_GB';
				break;
			case 'es':
			default:
				$lang = 'es_ES';
				break;
		}
		
		return $lang;
	}
}
