<?php
/**
 * IndexHomeController class.
 *
 * @author Manel Perez
 */

/**
 * Class IndexHomeController.
 *
 * Home controller.
 */
class IndexHomeController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		// Get next match information.
		$next_match		= $this->getData( 'MatchModel', 'getNextMatches', array( 'limit' => 1 ) );

		// Assign variables to template.
		$this->template->assign( 'next_match', $next_match );

		// Set main template.
		$this->template->setTemplate( 'home/index' );
	}
}