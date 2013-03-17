<?php
/**
 * AjaxMatchController class.
 *
 * @author Manel Perez
 */

include_once( PATH_LIBS . 'json_encode.php' );

/**
 * Class AjaxMatchController.
 *
 * Manage ajax actions for match events.
 */
class AjaxMatchController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		$post	= Factory::getInstance( 'FilterPost' );
		$op		= $post->getText( 'op' );

		switch ( $op )
		{
			case 'join-player':
				$output = $this->joinPlayer();
				break;

			default:
				$output	= array(
					'sate'		=> false,
					'data'		=> '',
					'msg_error'	=> 'Option not allowed'
				);
				break;
		}

		$this->setJsonHeaders();
		echo json_encode( $output );
		die;
	}

	/**
	 * Print an json response for ajax request.
	 */
	protected function joinPlayer()
	{
		$post		= Factory::getInstance( 'FilterPost' );
		$session	= Factory::getInstance( 'Session' );
		$user_model	= $this->getClass( 'MainUserController' );

		$id_match	= $post->getNumber( 'match' );
		$match		= $this->getData( 'MatchModel', 'getMatchById', array( 'id_match' => $id_match ) );

		$id_player	= $session->get( 'id_player' );
		if ( empty( $id_player )  )
		{
			// Error on request.
			return array(
				'state'		=> false,
				'data'		=> '',
				'msg_error'	=> 'Your session has expired. Please, reload page to join.'
			);
		}

		if ( $user_model->isPlayer() || ( !empty( $match[0] ) && 'friendly' == $match[0]['type'] ) )
		{
			$available	= $post->getString( 'available' );
			$player		= $this->getData( 'PlayerModel', 'getPlayerById', array( 'id_player' => $id_player ) );

			$is_closed_match = $this->getData( 'MatchModel', 'isClosedMatch', array( 'status' => $match[0]['status'], 'date_match' => $match[0]['day'] ) );
			if ( $is_closed_match )
			{
				$error_msg = 'The match call is closed';
			}
			else
			{
				$model_params = array(
					'id_match'	=> $id_match,
					'id_player'	=> $id_player,
					'available'	=> $available
				);

				if ( $this->getData( 'MatchModel', 'joinPlayerToMatch', $model_params ) )
				{
					$mail		= $this->getClass( 'Mail' );

					$mail->setSubject( "{$player[0]['name']} {$player[0]['middle_name']} is $available for match {$match[0]['day']}" );
					$mail->setBody( "The player {$player[0]['name']} {$player[0]['middle_name']} is $available for match {$match[0]['day']}" );
					$mail->setFrom( FROM_EMAIL );
					$mail->setReceiver( ADMIN_EMAIL );
					$mail->send();

					return array(
						'state' => true,
						'data'	=> array(
							'id_player'	=> $player[0]['id_player'],
							'name'		=> $player[0]['name'] . ' ' . $player[0]['middle_name'],
							'image_url'	=> $player[0]['image_url'],
							'player_url'=> $this->getClass( 'Url' )->buildUrl( 'player', 'index', array( $player[0]['sanitized_name'] ) ),
							'available'	=> $available
						)
					);
				}

				$error_msg = 'Error on save in database!';
			}
		}
		else
		{
			$error_msg = 'You are not allowed to play this game';
		}

		// Error on request.
		return array(
			'state'		=> false,
			'data'		=> '',
			'msg_error'	=> $error_msg
		);
	}

	/**
	 * Set Json headers to output.
	 */
	protected function setJsonHeaders()
	{
		header('Content-Type: text/javascript; charset=utf8');
	}
}
