<?php
/**
 * @version		$Id: archive.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class LDAPDirHelperQuery
{

    function queryusers($id=null) {

	$db =& JFactory::getDBO(); 

	$query1 = "select id, name, username, email, usertype, block, sendEmail, registerDate, lastvisitDate from #__users" . $where;

        $query2 = "select m.mid, m.name, m.displayname, m.usereditable, data"
    		 . " from #__ldapd_mapping as m"                                                                                                                                                                                
            	. " LEFT JOIN #__ldapd_userdata as d on m.mid = d.mid  ";

	$db->setQuery($query1);
	if ($id) 
	{
	    $result = $db->loadObject();
            $result->link = JRoute::_("index.php?option=com_ldapdirectory&view=user&user=" . $result->id);
            $result->cemail = JHTML::_('email.cloak', $result->email);                                                                                                                                                      
    	    $db->setQuery($query2 . " AND uid = $id ORDER BY mid" );
    	    $result->mdata = $db->loadObjectList(); 
	}
	else
	{
	    $result = $db->loadObjectList(); 
            foreach ($result as $id => $user) 
	    {
                    $result[$id]->link = JRoute::_("index.php?option=com_ldapdirectory&view=user&user=" . $user->id);                                                                                                                 
                    $result[$id]->cemail = JHTML::_('email.cloak', $user->email);                                                                                                                                                      
    		    $db->setQuery($query2 . " AND uid = $id ORDER BY mid" );
    		    $result[$id]->mdata = $db->loadObjectList(); 
            }  
	}
	return $result;
    }

    function getuserimage($uid) {
	$db =& JFactory::getDBO(); 

        $query = "select data, blobdata"
    		 . " from #__ldapd_userdata as m"                                                                                                                                                                                
		 . " WHERE mid = 2 AND uid = " . $uid;

	$db->setQuery($query);
    	$tmp = $db->loadObjectList(); 
	return $tmp[0];
    }

}
