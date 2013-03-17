<?php
/**
 * ScoreMatchController class.
 *
 * @author Manel Perez
 */

/**
 * Class ScoreMatchController.
 *
 * Manage actions related with a match call.
 */
class ScoreMatchController extends Controller
{
	/**
	 * Number of days that is available vote.
	 *
	 * @var integer
	 */
	const DAYS_FOR_VOTE = 6;

	/**
	 * Launch the execution.
	 */
	public function run()
	{
		if ( $this->getClass( 'MainUserController' )->isLogged() && $this->getClass( 'MainUserController' )->isPlayer() )
		{
			if ( true === $this->checkMatchToScore() )
			{
				if ( Factory::getInstance( 'FilterPost' )->exists( 'scored' ) )
				{
					$this->runPost();
				}
				else
				{
					$this->runSimple();
				}
			}
			else
			{
				// Set main template.
				$this->template->assign( 'message', 'Sorry but, you have scored this match already or the deadline to score the match is exhausted.' );
				$this->template->setTemplate( 'layout/simple_message' );
			}
		}
		else
		{
			// Set main template.
			$this->template->setTemplate( 'error/forbidden' );
		}

		$this->template->assign( 'acvite_tab', 'match' );
	}

	/**
	 * Run the post execution.
	 */
	protected function runSimple()
	{
		// Get last match information.
		$match		= $this->getData( 'MatchModel', 'getLastMatches', array( 'limit' => 1 ) );
		$players	= $this->getData( 'MatchModel', 'getPlayersForMatch', array(
			'id_match' => $match[0]['id_match'],
			'available_state' => array( 'called' ),
			'order_by'	=> 'p.id_player ASC',
			'exclude_player' => Factory::getInstance( 'Session' )->get( 'id_player' )
		) );

		// Assign vars to template.
		$this->template->assign( 'match', $match );
		$this->template->assign( 'players', $players );

		// Set main template.
		$this->template->setTemplate( 'match/score' );
	}

	/**
	 * Run the post execution.
	 */
	protected function runPost()
	{
		$post = Factory::getInstance( 'FilterPost' );

		$id_player_scorer	= Factory::getInstance( 'Session' )->get( 'id_player' );
		$id_match			= $post->getNumber( 'id_match' );
		$score_info			= $post->getArray( 'player' );

		$match				= $this->getData( 'MatchModel', 'getLastMatches', array( 'limit' => 1 ) );

		// Saving match.
		$this->getData( 'MatchModel', 'scoreMatchPlayers', array( 'id_match' => $match[0]['id_match'], $id_player_scorer, $score_info ) );

		$this->template->assign( 'message', 'Thanks for score!' );
		$this->template->setTemplate( 'layout/simple_message' );
	}

	/**
	 * Check if the match is active and user can score these match.
	 *
	 * @return boolean
	 */
	protected function checkMatchToScore()
	{
		$id_player_scorer	= Factory::getInstance( 'Session' )->get( 'id_player' );

		$match				= $this->getData( 'MatchModel', 'getLastMatches', array( 'limit' => 1 ) );
		$player_in_match	= $this->getData( 'MatchModel', 'isPlayerInMatch', array(
			'id_match' => $match[0]['id_match'],
			'id_player' => $id_player_scorer,
			'available_state' => array( 'called' )
		) );
		$has_scored			= $this->getData( 'MatchModel', 'hasPlayerScored', array( 'id_match' => $match[0]['id_match'], 'id_player_scorer' => $id_player_scorer ) );

		// The people have 6 days to vote.
		$ago_time 	= time() - ( self::DAYS_FOR_VOTE * 24 * 3600 );
		$match_time = strtotime( $match[0]['day'] );

		return ( !$has_scored && $player_in_match && ( $ago_time < $match_time ) && ( time() > $match_time ) );
	}
}