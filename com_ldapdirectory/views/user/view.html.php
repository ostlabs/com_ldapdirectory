<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class ldapdirViewUser extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		// Show all the groups

                $model =& $this->getModel();
                $user = $model->getUser(JRequest::getInt('user'));
		$this->assignRef('user', $user);

		parent::display($tpl);

	}
}
