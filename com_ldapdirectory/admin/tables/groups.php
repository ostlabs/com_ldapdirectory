<?php
/**
 * @version		$Id: banner.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Banners
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


/**
 * @package		Joomla
 * @subpackage	Banners
 */
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