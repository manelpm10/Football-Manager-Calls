<?php
/**
 * IndexPlayerController class.
 *
 * @author Manel Perez
 */

/**
 * Class IndexPlayerController.
 *
 * Home controller.
 */
class IndexPlayerController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		$params	= $this->getUrlArguments();

		// Get next match information.
		$player	= $this->getData( 'PlayerModel', 'getPlayerBySanitizedName', $params );

		// Assign variables to template.
		$this->template->assign( 'acvite_tab', 'team' );
		$this->template->assign( 'player', $player[0] );

		// Set main template.
		$this->template->setTemplate( 'player/index' );
	}

	protected function getUrlArguments()
	{
		$get		= Factory::getInstance( 'FilterGet' );

		return array(
			'sanitized_name'	=> $get->getString( '0' )
		);
	}
}