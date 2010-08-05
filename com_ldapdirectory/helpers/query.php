<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class LDAPDirHelperQuery
{

    function queryusers($id=null, $group=null) {

	$db =& JFactory::getDBO();

	$where = "";

	if (!is_null($id)) $where = " WHERE id = $id";

        if (!is_null($group)) $where = " LEFT JOIN #__ldapd_userdata as d on d.uid = u.id WHERE d.mid=1 AND d.data=" . $group;

	$query1 = "select u.id, name, username, email, usertype, block, sendEmail, registerDate, lastvisitDate from #__users as u" . $where;

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
	    foreach ($db->loadAssocList() as $object ) {
		$result->mdata[$object['name']]['data'] = $object['data'];
		$result->mdata[$object['name']]['displayname'] = $object['displayname'];
	    }
	}
	else
	{
	    $result = $db->loadObjectList();
            foreach ($result as $id => $user)
	    {
                    $result[$id]->link = JRoute::_("index.php?option=com_ldapdirectory&view=user&user=" . $user->id);
                    $result[$id]->cemail = JHTML::_('email.cloak', $user->email);
    		    $db->setQuery($query2 . " AND uid = " . $user->id ." ORDER BY mid" );
		    foreach ($db->loadAssocList() as $object ) {
			$result[$id]->mdata[$object['name']]['data'] = $object['data'];
			$result[$id]->mdata[$object['name']]['displayname'] = $object['displayname'];
		    }
            }
	}
	return $result;
    }

    function querygroups ($basegroup) {
	$db =& JFactory::getDBO();
	$result=array();
	$query = "SELECT id, name, description FROM #__ldapd_groups WHERE published=1 AND parent=" . $basegroup;
	$db->setQuery($query);
        foreach ($db->loadObjectList() as $id => $object ) {
	    $result[$id]=$object;
            $result[$id]->link = JRoute::_("index.php?option=com_ldapdirectory&view=groups&group=" . $object->id);
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
