<?php

class Httphandler
{
	/**
	 * cURL Handler.
	 */
	protected $ch;

	/**
	 * Timeout connexion.
	 */
	protected $timeout			= 300;

	/**
	 * Maximum number of redirections allowed.
	 */
	protected $max_redirections	= 5;

	/**
	 * User agent information.
	 */
	protected $user_agent		= 'Mozilla/5.0 (Windows; U; Windows NT 5.1; es-ES; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 (.NET CLR 3.5.30729)';

	/**
	 * Cookie file.
	 */
	protected static $cookie;

	/**
	 * The response headers from last request.
	 */
	protected $response_headers;

	/**
	 * The response data from last request.
	 */
	protected $response_data;

	/**
	 * Proxy IP and Port 'XXX.XXX.XXX.XXX:PPPP'
	 */
	protected $proxy;

	/**
	 * Proxy Auth chain (base64).
	 */
	protected $proxy_auth = false;

	/**
	 * Set a new timeout.
	 *
	 * @param integer $timeout Timeout in seconds.
	 * @return null
	 */
	public function setTimeout( $timeout )
	{
		$this->timeout = $timeout;

		curl_setopt( $this->ch, CURLOPT_TIMEOUT,		$this->timeout );
	}

	/**
	 * Set a new max redirections.
	 *
	 * @param integer $max_redirections Max redirections number.
	 * @return null
	 */
	public function setMaxRedirections( $max_redirections )
	{
		$this->max_redirections = $max_redirections;

		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION,	(bool) $this->max_redirections );
		curl_setopt( $this->ch, CURLOPT_MAXREDIRS,		$this->max_redirections );
	}

	/**
	 * Set a new user agent.
	 *
	 * @param string $user_agent New user anget.
	 * @return null
	 */
	public function setUserAgent( $user_agent )
	{
		$this->user_agent = $user_agent;

		curl_setopt( $this->ch, CURLOPT_USERAGENT,		$this->user_agent );
	}

	/**
	 * Set a new proxy (empty value for no proxy use proxy).
	 *
	 * @param string $proxy Set proxy in format (XXX.XXX.XXX.XXX:PPPP).
	 * @return null
	 */
	public function setProxy( $proxy_ip, $proxy_port, $proxy_login = false, $proxy_password = false )
	{
		$this->proxy_auth = false;
		if ( $proxy_login !== false && $proxy_password !== false )
		{
			$this->proxy_auth = base64_encode( $proxy_login . ':' . $proxy_password );
		}
		$this->proxy = $proxy_ip . ':' . $proxy_port;
		curl_setopt( $this->ch, CURLOPT_PROXY, $this->proxy );
	}

	/**
	 * Return the headers get on last response.
	 *
	 * @return array
	 */
	public function getResponseHeaders()
	{
		return $this->response_headers;
	}

	/**
	 * Initialize the curl handler.
	 */
	public function __construct()
	{
		// Init cookie.
		self::$cookie	= tempnam( null, 'http_browser_' );

		// Init cURL.
		$this->ch		= curl_init();

		// Set default options.
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER,	false );
		curl_setopt( $this->ch, CURLOPT_USERAGENT,		$this->user_agent );
		curl_setopt( $this->ch, CURLOPT_ENCODING,		'gzip,deflate' );
		curl_setopt( $this->ch, CURLOPT_TIMEOUT,		$this->timeout );
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION,	(bool) $this->max_redirections );
		curl_setopt( $this->ch, CURLOPT_MAXREDIRS,		$this->max_redirections );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER,	true );
		curl_setopt( $this->ch, CURLOPT_COOKIEJAR,		self::$cookie );
		curl_setopt( $this->ch, CURLOPT_COOKIEFILE,		self::$cookie );
	}

	/**
	 * Destruct initialized objects.
	 */
	public function __destruct()
	{
		// Close cURL.
		if ( isset( $this->ch ) )
		{
			curl_close( $this->ch );
		}
	}

	/**
	 * Send a request and return the response data.
	 *
	 * @param string $url The url to retrieve.
	 * @param array $headers An array of HTTP header fields to set.
	 * @param mixed $post_data This can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with the field name as key and field data as value. If value  is an array, the Content-Type header will be set to multipart/form-data.
	 * @param string $referer The contents of the "Referer: " header to be used in a HTTP request.
	 * @return string
	 */
	public function getResponse( $url, $headers = false, $post_data = false, $referer = false )
	{
		$headers = $this->buildRequestHeaders( $headers );

		// Set url.
		curl_setopt( $this->ch, CURLOPT_URL,			$url );

		// Set headers.
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER,		$headers );

		if( isset( $referer ) )
		{
			curl_setopt( $this->ch, CURLOPT_REFERER,	$referer );
		}

		// Set posted data.
		if ( $post_data !== false && !empty( $post_data ) )
		{
			curl_setopt( $this->ch, CURLOPT_POSTFIELDS,	$post_data );
			curl_setopt( $this->ch, CURLOPT_POST,		true );
		}

		// Get data.
		$this->response_data	= curl_exec( $this->ch );

		// Get response headers.
		$this->response_headers	= curl_getinfo($this->ch);

		return $this->response_data;
	}

	/**
	 * Return an array with request headers.
	 *
	 * @param array $headers More headers information. 
	 * @return array
	 */
	protected function buildRequestHeaders( $headers = false )
	{
		$default_headers = array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: es-es,es;q=0.8,en-gb;q=0.5,en;q=0.3',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Keep-Alive: 300'
		);

		if ( $this->proxy_auth !== false )
		{
			$default_headers[] = 'Proxy-Authorization: Basic ' . $this->proxy_auth;
		}

		if ( $headers !== false && !empty( $headers ) )
		{
			if ( !is_array( $headers ) )
			{
				$headers = array( $headers );
			}

			$headers = array_merge( $default_headers, $headers );
		}
		else
		{
			$headers = $default_headers;
		}

		return $headers;
	}
}
