<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

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
