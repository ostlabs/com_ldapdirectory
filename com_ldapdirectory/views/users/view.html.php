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

class ldapdirViewUsers extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		// Show all the groups
		$model =& $this->getModel();
		$users = $model->getUsers();
		$this->assignRef('users', $users);

		parent::display($tpl);

	}
}
