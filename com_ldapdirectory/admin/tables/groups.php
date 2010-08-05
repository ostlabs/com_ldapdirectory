<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Tablegroups extends JTable
{

	var $id = 0;
	var $name = '';
	var $published = 0;
	var $parent = 0;
	var $ordering = 0;
	var $checked_out = 0;
	var $checked_out_time = 0;
	var $lvalue = '';
	var $description = '';
	var $image_type = '';
	var $image = NULL;

	function __construct( &$_db )
	{
		parent::__construct( '#__ldapd_groups', 'id', $_db );

	}


	function getGroup ($groupname, $autocreate) {
	    $query = "SELECT id FROM #__ldapd_groups WHERE lvalue = '" . $groupname . "'";
            $this->_db->setQuery($query);
            $gID = $this->_db->loadResult();
	    if (!$gID && $autocreate) {
		$this->name = $this->lvalue = $groupname;
		$this->store();
		echo "Autocreated Group: " . $this->id;
		$gID = $this->id;
	    }
	    return $gID;
	}

}