<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class ldapdirViewGroups extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		// Show all the groups
		$basegroup = JRequest::getInt('group', 0);

		$model =& $this->getModel();
		$groups = $model->getGroups($basegroup);
		$users = $model->getUsers($basegroup);

		$this->assignRef('groups', $groups);
		$this->assignRef('users', $users);

		parent::display($tpl);

	}
}
