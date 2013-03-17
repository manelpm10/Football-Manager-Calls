<?php
/**
 * CallMatchController class.
 *
 * @author Manel Perez
 */

/**
 * Class CallMatchController.
 *
 * Manage actions related with a match call.
 */
class CallMatchController extends Controller
{
	/**
	 * Available types.
	 *
	 * var $array.
	 */
	var $types = array( 'available', 'unavailable', 'injuried', 'if_necessary' );

	/**
	 * Launch the execution.
	 */
	public function run()
	{
		$user_model = $this->getClass( 'MainUserController' );
		if ( $user_model->isLogged() )
		{
			$get = Factory::getInstance( 'FilterGet' );

			if ( $get->exists( 0 ) )
			{
				$id_match	= $get->getString( 0 );
				$match		= $this->getData( 'MatchModel', 'getMatchById', array( 'id_match' => $id_match ) );
			}
			else
			{
				// Get next match information.
				$match	= $this->getData( 'MatchModel', 'getNextMatches', array( 'id_season' => false, 'limit' => 1 ) );
			}

			if ( !empty( $match ) && ( 'friendly' == $match[0]['type'] || $user_model->isPlayer() ) )
			{
				$players	= $this->getData( 'MatchModel', 'getPlayersForMatchWithStatistics', array( 'id_match' => $match[0]['id_match'], 'id_season' => $match[0]['id_season'] ) );
				$players	= ( empty( $players ) )? array() : $players;

				$players_not_joined = $this->getData( 'MatchModel', 'getPlayersNotJoinedToMatch', array( 'id_match' => $match[0]['id_match'], 'match_type' => $match[0]['type'] ) );

				$match[0]['closed'] = $this->getData( 'MatchModel', 'isClosedMatch', array( 'status' => $match[0]['status'], 'date_match' => $match[0]['day'] ) );
				$has_voted	= $this->getData( 'MatchModel', 'isPlayerInMatch', array( 'id_match' => $match[0]['id_match'], 'id_player' => Factory::getInstance( 'Session' )->get( 'id_player' ) ) );

				// Assign variables.
				$this->template->assign( 'types', $this->types );
				$this->template->assign( 'match', $match[0] );
				$this->template->assign( 'is_match_played', ( time() > strtotime( "{$match[0]['day']} {$match[0]['hour']}" ) ) );
				$this->template->assign( 'is_admin', $user_model->isAdmin() );
				$this->template->assign( 'players', $players );
				$this->template->assign( 'players_not_joined', $players_not_joined );
				$this->template->assign( 'players_not_joined_count', count( $players_not_joined ) );
				$this->template->assign( 'has_voted', $has_voted );

				// Set main template.
				$this->template->setTemplate( 'match/call' );
			}
			elseif ( !empty( $match ) )
			{
				// Set main template no match.
				$this->template->assign( 'type', 'msg_warning' );
				$this->template->assign( 'message', _( 'Ooooops, you are not allowed to see this match.' ) );
				$this->template->setTemplate( 'layout/simple_message' );
			}
			else
			{
				// Set main template no match.
				$this->template->assign( 'type', 'msg_warning' );
				$this->template->assign( 'message', _( 'We don\'t have any match planned for this week.' ) );
				$this->template->setTemplate( 'layout/simple_message' );
			}

			$this->template->assign( 'acvite_tab', 'match' );
		}
		else
		{
			// Set main template.
			$this->template->setTemplate( 'error/forbidden' );
		}
	}
}
