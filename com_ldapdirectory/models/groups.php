<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class LDAPDirModelGroups extends JModel
{

    function getUsers($basegroup) {
        return LDAPDirHelperQuery::queryusers(null, $basegroup);
    }

    function getGroups($basegroup) {
        return LDAPDirHelperQuery::querygroups($basegroup);
    }

}
