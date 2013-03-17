<?php
/**
 * Store and retrieve user messages in session. Session is always included at this point, even in Unit testing.
 */

/**
 * Class to manage user messages when are shown after redirects.
 */
class FlashMessage
{
	/**
	 * The message is shown until the user presses in the "x" mark.
	 *
	 * @var boolean
	 */
	const PERSISTENT = true;

	/**
	 * Default value, it's automatically deleted from the messages list when shown.
	 *
	 * @var boolean
	 */
	const VOLATILE = false;

	/**
	 * Session group where the messages are stored.
	 *
	 * @var string
	 */
	public static $session_group = 'flash_message';

	/**
	 * Variable used for message storage in session.
	 *
	 * @var string
	 */
	public static $var_name = 'messages';

	/**
	 * Store the message in session.
	 *
	 * @param string $message The message string.
	 * @param string $class The class associated to this message, depending on the result.
	 * @param boolean $persistence Keep the message or not in the session after being shown.
	 */
	static public function set( $message, $class = 'msg_ok', $persistence = self::VOLATILE )
	{
		$existing_messages = self::_getMsgs();

		$existing_messages[] = array(
			'message' => $message,
			'class' => $class,
			'persistence' => $persistence
		);

		self::_setMsgs( $existing_messages );
	}

	/**
	 * Delete a persistent message from a given array position.
	 *
	 * @param integer $position The array key of the messages stack.
	 */
	static public function delete( $position )
	{
		$messages = self::_getMsgs();
		if ( isset( $messages[$position] ) )
		{
			unset ( $messages[$position] );
			self::_setMsgs( $messages );
			return true;
		}

		return false;
	}

	/**
	 * Returns the messages stack.
	 */
	static public function get()
	{
		$messages = array();
		$existing_messages = self::_getMsgs();

		// Discard volatile messages and store again only the persistent ones.
		foreach ( $existing_messages as $key => $m )
		{
			if ( self::PERSISTENT == $existing_messages[$key]['persistence'] )
			{
				$messages[] = $m;
			}
		}

		self::_setMsgs( $messages );

		return $existing_messages;
	}

	/**
	 * Destroys the session storing flash messages.
	 *
	 */
	static public function flush()
	{
		Factory::getInstance( 'Session' )->remove( self::$session_group );
	}

	/**
	 * Get the messages stack from session.
	 *
	 * @return array
	 */
	static private function _getMsgs()
	{
		$msgs = Factory::getInstance( 'Session' )->get( self::$var_name );

		if ( $msgs )
		{
			return $msgs;
		}

		return array();
	}

	/**
	 * Write in the messages stack the data provided.
	 *
	 * @param array $data Array of data to store in session.
	 */
	static private function _setMsgs( Array $data )
	{
		Factory::getInstance( 'Session' )->set( self::$var_name, $data );
	}
}
