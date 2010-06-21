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
class Tablemappings extends JTable
{

	var $mid = 0;
	var $name = "";
	var $displayname = "";
	var $usereditable = 0;
	var $fromldap = 0;
	var $ldapfield = "";
	var $ldapwins = 0;
	var $checked_out = 0;

	function __construct( &$_db )
	{
		parent::__construct( '#__ldapd_mapping', 'mid', $_db );

	}


	function getmappings()
	{
            $query = "select *"                                                                                                                                                                            
                     . " from #__ldapd_mapping"; 
            $this->_db->setQuery($query);                                                                                                                                                                                            
            return $this->_db->loadAssocList();
	}
}
