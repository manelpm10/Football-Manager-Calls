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
class IndexInfoController extends Controller
{
	/**
	 * Launch the execution.
	 */
	public function run()
	{
		// Assign variables.
		$this->template->assign( 'acvite_tab', 'information' );

		// Set main template.
		$this->template->setTemplate( 'info/index' );
	}
}