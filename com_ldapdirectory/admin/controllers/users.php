<?php
/**
 * @version		$Id: client.php 17299 2010-05-27 16:06:54Z ian $
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

jimport( 'joomla.application.component.controller' );

/**
 * @package		Joomla
 * @subpackage	Banners
 */
class LDAPDirControllerUsers extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
//		$this->registerTask( 'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function handletask($task) {

	    if ($task == "") $task="display";

	    $this->_task = $task;

	    switch ($task)
	    {
		default:
		    echo "Not implemented yet: " . $task;
		    break;

		case "cancel":
		    $this->cancel();
		    break;

		case "apply":
		case "save":
		    $this->save();
		    break;

		case "edit":
		    $this->edit();
		    break;

		case "remove":
		    $this->remove();
		    break;

		case "display":
		    $this->display();
		    break;
	    }


	}


	function display()
	{
		global $mainframe;

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$context			= 'com_ldapdirectory.users.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',	'filter_order',	 'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$search				= $mainframe->getUserStateFromRequest( $context.'search', 'search', '',	'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit',		'limit',		$mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';

		// get the total number of records
		$query = 'SELECT a.*, count(u.id) as datacount'
		. ' FROM #__users AS a'
		. ' LEFT JOIN #__ldapd_userdata AS u ON u.uid = a.id'
		. ' GROUP BY a.id'
		. $where
		. $orderby
		;

		$db->setQuery( $query );
		$db->query();
		$total = $db->getNumRows();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );

		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $db->loadObjectList();

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;


        	require_once(JPATH_COMPONENT.DS.'views'.DS.'users.php');
		LDAPDirViewUsers::displayusers( $rows, $pageNav, $lists );
	}

	/**
	 * Edit a banner client record
	 */
	function edit()
	{
		// Initialize variables
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		$userId	= $user->get ( 'id' );

		if ($this->_task == 'edit') {
			$cid	= JRequest::getVar('cid', array(0), 'method', 'array');
		} else {
			$cid	= array( 0 );
		}

		$row =& JTable::getInstance('users', 'Table');
		$data = $row->getuser( (int) $cid[0] );
		$row =& JTable::getInstance('mappings', 'Table');
		$mappings = $row->getmappings();

		require_once(JPATH_COMPONENT.DS.'views'.DS.'users.php');
		LDAPDirViewUsers::edit( $data, $mappings);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=users' );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('users', 'Table');

		$kimage	= JRequest::getVar('kimage', 1, 'method', 'int');
		$cid	= JRequest::getVar('cid', 0, 'method', 'int');
		$userd  = JRequest::getVar('userd', array(0), 'method', 'array');
		$mappings = JRequest::getVar('mapping', array(0), 'method', 'array');

		$user = JFactory::getuser($cid);

		if (!$user->bind($userd)) { // now bind the data to the JUser Object, if it not works....

		    JError::raiseWarning('', JText::_( $user->getError())); // ...raise an Warning
		    return false; // if you're in a method/function return false

		}

		if (!$user->save()) { // if the user is NOT saved...

		    JError::raiseWarning('', JText::_( $user->getError())); // ...raise an Warning
		    return false; // if you're in a method/function return false

		}

		foreach ($mappings as $mid => $data)
		{
		    $table->storedata($cid, $mid, $data, 1);

		}

		if (!$kimage) {
		    $table->deleteimage($cid);
		}

		switch (JRequest::getCmd( 'task' ))
		{
			case 'apply':
				$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=users&task=edit&cid[]='. $cid );
				break;
		}


		$this->setMessage( JText::_( 'Item Saved' ) );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=users' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$table		=& JTable::getInstance('mappings', 'Table');
		$table->cid	= JRequest::getVar( 'mid', 0, 'post', 'int' );
		$table->checkin();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=users' );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$mid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$table	=& JTable::getInstance('users', 'Table');
		$n		= count( $mid );

		for ($i = 0; $i < $n; $i++)
		{
		    $query = "select id from #__ldapd_userdata where uid = " . $mid[$i];
		    $db->setQuery($query);
		    $rows = $db->loadObjectList();
		    foreach ($rows as $row) {
			$table->delete($row->id);
		    }
		    // TODO: Delete fields from users table
		    $user = &JFactory::getUser($mid[$i]);
		    $user->delete();

		}

		$this->setMessage( JText::sprintf( 'Items removed', $n ) );
	}

}

