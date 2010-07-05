<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class LDAPDirModelUsers extends JModel
{

    function getUsers() {
	return LDAPDirHelperQuery::queryusers();
    }
}
