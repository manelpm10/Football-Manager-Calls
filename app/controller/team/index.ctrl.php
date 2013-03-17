<?php
/**
 * IndexTeamController class.
 *
 * @author Manel Perez
 */

/**
 * Class IndexTeamController.
 *
 * Home controller.
 */
class IndexTeamController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		// Get players.
		$team		= $this->getData( 'TeamModel', 'getPlayers', array( 'type' => 'player' ) );

		// Assign variables to template.
		$this->template->assign( 'team', $team );
		$this->template->assign( 'acvite_tab', 'team' );

		// Set main template.
		$this->template->setTemplate( 'team/index' );
	}
}