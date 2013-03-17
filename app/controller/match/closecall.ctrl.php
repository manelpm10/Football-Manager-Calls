<?php
/**
 * ClosecallMatchController class.
 *
 * @author Manel Perez
 */

/**
 * Class ClosecallMatchController.
 *
 * Manage actions related with a match call.
 */
class ClosecallMatchController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		$post		= Factory::getInstance( 'FilterPost' );
		$url		= $this->getClass( 'Url' );
		$user_model	= $this->getClass( 'MainUserController' );

		$id_match	= $post->getNumber( 'id_match' );
		$players	= array_keys( $post->getArray( 'player' ) );
		$match		= $this->getData( 'MatchModel', 'getMatchById', array( 'id_match' => $id_match ) );

		if ( $user_model->isLogged() && $user_model->isAdmin() && !empty( $players ) )
		{
			$mail		= $this->getClass( 'Mail' );

			// Set called players.
			$this->getData( 'MatchModel', 'setCalledPlayers', array( 'id_match' => $id_match, 'players' => $players ) );

			// Close call.
			$this->getData( 'MatchModel', 'closeMatch', array( 'id_match' => $id_match ) );

			$called_players		= $this->getData( 'PlayerModel', 'getPlayerById', array( 'id_player' => $players ) );
			$available_players	= $this->getData( 'MatchModel', 'getPlayersForMatch', array( 'id_match' => $id_match, 'available_state' => array( 'available', 'if_necessary' ) ) );

			$body = "The call has been closed.\n\n";
			$body .= "Rival: {$match[0]['rival']}\n";
			$body .= "When: {$match[0]['day']} at {$match[0]['hour']} (meet 30 minutes before match)\n";
			if ( 'friendly' != $match[0]['type'] )
			{
				$body .= "Where: http://goo.gl/MBNC9\n";
			}
			$body .= "Called players:\n\n";
			foreach ( $called_players as $called )
			{
				$mail->setReceiver( $called['email'] );
				$body.= "\t{$called['name']} {$called['middle_name']}\n";
			}
			$body.= "\nGood luck!\n";

			$mail->setSubject( "Final call for match {$match[0]['day']}" );
			$mail->setBody( $body );
			$mail->setFrom( FROM_EMAIL );

			foreach ( $available_players as $available )
			{
				$mail->setCc( $available['email'] );
			}

			$mail->send();
		}

		$call_url = $url->buildUrl( 'match', 'call', array( $match[0]['id_match'] ) );
		header( "Location:$call_url" );
		die;
	}
}