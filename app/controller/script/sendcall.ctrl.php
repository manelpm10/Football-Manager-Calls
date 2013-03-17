<?php

class SendcallScriptController extends Controller
{
	/**
	 * Number the days before the match to send call.
	 */
	const NUM_DAYS_BEFORE_MATCH = 5;

	/**
	 * Number the days before match to send reminder.
	 */
	const NUM_DAYS_BEFORE_MATCH_REMINDER = 3;

	/**
	 * Launch the concrete script.
	 */
	public function execute()
	{
		$this->executeCall();
		$this->executeReminder();
	}

	/**
	 * Checks if in NUM_DAYS_BEFORE_MATCH we have a match. In afirmative case, send an email with call.
	 *
	 * @return null
	 */
	protected function executeCall()
	{
		$mail	= $this->getClass( 'Mail' );
		$date	= date( 'Y-m-d', ( time() + ( self::NUM_DAYS_BEFORE_MATCH * 24 * 60 * 60 ) ) );

		$match	= $this->getData( 'MatchModel', 'getMatchByDate', array( 'date' => $date ) );
		if ( !isset( $match[0] ) )
		{
			return;
		}

		$match					= array_shift( $match );
		$match['day_formated']	= date( 'd/m/Y', strtotime( $match['day'] ) );

		$players	= $this->getData( 'TeamModel', 'getPlayers', array( 'type' => 'player' ) );
		foreach( $players as $player )
		{
			$mail->setReceiver( $player['email'] );
		}

		$mail->setContentType( 'text/html', 'utf8' );
		$mail->setFrom( FROM_EMAIL );
		$mail->setSubject( "Call {$match['day_formated']}" );
		$mail->setBody( $this->composeMailBody( $match ) );
		$mail->send();
	}

	/**
	 * Checks if in NUM_DAYS_BEFORE_MATCH_REMINDER are players that not provaided availability.
	 *
	 * @return null
	 */
	protected function executeReminder()
	{
		$url	= $this->getClass( 'Url' );
		$mail	= $this->getClass( 'Mail' );
		$date	= date( 'Y-m-d', ( time() + ( self::NUM_DAYS_BEFORE_MATCH_REMINDER * 24 * 60 * 60 ) ) );

		$match	= $this->getData( 'MatchModel', 'getMatchByDate', array( 'date' => $date ) );
		if ( !isset( $match[0] ) )
		{
			return;
		}

		$match					= array_shift( $match );
		$match['day_formated']	= date( 'd/m/Y', strtotime( $match['day'] ) );
		$join_url				= $url->buildUrl( 'Match', 'Call', array( $match['id_match'] ) );

		$mail->setContentType( 'text/html', 'utf8' );
		$mail->setFrom( FROM_EMAIL );
		$mail->setSubject( "Call {$match['day_formated']}. Are you going to play?" );
		$mail->setBody( "We are going to play versus '{$match['rival']}' in " . self::NUM_DAYS_BEFORE_MATCH_REMINDER . " days and you aren't provided availability.\n\nIn order to close match as soon as possible, please provide here your availability <a href='$join_url'>$join_url</a>\n\nThank you" );

		$players	= $this->getData( 'MatchModel', 'getPlayersNotJoinedToMatch', array( 'id_match' => $match['id_match'], 'match_type' => $match['type'] ) );
		foreach( $players as $player )
		{
			$mail->setReceiver( $player['email'] );
			$mail->send();
			$mail->resetReceiver();
		}
	}

	/**
	 * Compose the mail body.
	 *
	 * @param array $match Array with match information.
	 * @return string
	 */
	protected function composeMailBody( $match )
	{
		$url = $this->getClass( 'Url' );

		$join_url = $url->buildUrl( 'Match', 'Call', array( $match['id_match'] ) );

		return <<<HTML
<html>
<head>
	<title>Call {$match['day_formated']}</title>
</head>
<body>
	<p>New football match!</p>
	<table>
		<tbody>
			<tr>
				<td><strong>When</strong></td>
				<td>{$match['day_formated']} {$match['hour']}</td>
			</tr>
			<tr>
				<td><strong>Rival</strong></td>
				<td>{$match['rival']}</td>
			</tr>
			<tr colspan="2">
				<td><strong><a href="$join_url">Join here!</a></strong></td>
			</tr>
		</tbody>
	</table>
</body>
HTML;
	}
}