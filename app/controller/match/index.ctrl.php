<?php
/**
 * IndexMatchController class.
 *
 * @author Manel Perez
 */

/**
 * Class IndexMatchController.
 *
 * Home controller.
 */
class IndexMatchController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		$get = Factory::getInstance( 'FilterGet' );
		if ( $get->exists( 'season' ) )
		{
			$id_season = $get->getString( 'season' );
		}
		else
		{
			$season		= $this->getData( 'SeasonModel', 'getLastSeason', array( 'limit' => 3 ) );
			$season		= array_shift( $season );
			$id_season	= $season['id_season'];
		}

		if ( $get->exists( 'season' ) )
		{
			$next_matches	= array();
			$last_matches	= $this->getData( 'MatchModel', 'getLastMatches', array( 'id_season' => $id_season ) );
		}
		else
		{
			// Get next and latests match information.
			$next_matches	= $this->getData( 'MatchModel', 'getNextMatches', array( 'id_season' => $id_season, 'limit' => 5 ) );
			$last_matches	= $this->getData( 'MatchModel', 'getLastMatches', array( 'id_season' => $id_season, 'limit' => 5 ) );
		}

		// Assign variables to template.
		$this->template->assign( 'acvite_tab', 'match' );
		$this->template->assign( 'next_matches', $next_matches );
		$this->template->assign( 'last_matches', $last_matches );

		// Set main template.
		$this->template->setTemplate( 'match/index' );
	}
}