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
class LDAPDirControllerMappings extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',		'edit' );
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

		case "editable":
		case "noteditable":
		case "ldapwins":
		case "ldaplooses":
		    $this->toggle($task);
		    break;

		case "cancel":
		    $this->cancel();
		    break;

		case "apply":
		case "save":
		    $this->save();
		    break;

		case "edit":
		case "add":
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
		$context			= 'com_ldapdirectory.mappings.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',	'filter_order',	 'a.mid', 'cmd' );
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
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.mid';

		// get the total number of records
		$query = 'SELECT a.*'
		. ' FROM #__ldapd_mapping AS a'
		. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
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


        	require_once(JPATH_COMPONENT.DS.'views'.DS.'mappings.php');
		LDAPDirViewmappings::mappings( $rows, $pageNav, $lists );
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


		$row =& JTable::getInstance('mappings', 'Table');
		$row->load( (int) $cid[0] );

		// fail if checked out not by 'me'
		if ($row->isCheckedOut( $userId )) {
			$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=mappings' );
			return JError::raiseWarning( JText::sprintf( 'WARNEDITEDBYPERSON', $row->name ) );
		}

		if ($row->cid) {
			// do stuff for existing record
			$row->checkout( $userId );
		} else {
			// do stuff for new record
			$row->published = 0;
			$row->approved = 0;
		}

		require_once(JPATH_COMPONENT.DS.'views'.DS.'mappings.php');
		LDAPDirViewmappings::mapping( $row );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=mappings' );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('mappings', 'Table');

		if (!$table->bind( JRequest::get( 'post' ) )) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		if (!$table->check()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		if (!$table->store()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		$table->checkin();

		switch (JRequest::getCmd( 'task' ))
		{
			case 'apply':
				$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=mappings&task=edit&cid[]='. $table->mid );
				break;
		}

		$this->setMessage( JText::_( 'Item Saved' ) );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=mappings' );

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

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=mappings' );

		$db		=& JFactory::getDBO();
		$mid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );



		// Initialize variables
		$table	=& JTable::getInstance('mappings', 'Table');
		$utable	=& JTable::getInstance('users', 'Table');
		$n		= count( $mid );

		for ($i = 0; $i < $n; $i++)
		{
		    if ($mid[$i] == 1 || $mid[$i] == 2) {
            		return JError::raiseWarning( 500, "You may not delete Static mappings for GroupID or Picture as these are hard coded mappings" );
		    } else {
			$query = "select id from #__ldapd_userdata where mid = " . $mid[$i];
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row) {
			    $utable->delete($row->id);
			}

                	if (!$table->delete( (int) $mid[$i] )) {
                	    return JError::raiseWarning( 500, $table->getError() );
                	}
		    }
		}

		$this->setMessage( JText::sprintf( 'Items removed', $n ) );
	}

	function toggle($task)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$n			= count( $cid );

		switch ($task) {
		    case "editable":
		        $toggle		= 'usereditable = 0' ;
			$message	= 'Made no longer user editable';
			break;
		    case "noteditable":
		        $toggle		= 'usereditable = 1' ;
			$message	= 'Made user editable';
			break;
		    case "ldapwins":
		        $toggle		= 'ldapwins = 0' ;
			$message	= 'LDAP no longer wins on conflict';
			break;
		    case "ldaplooses":
		        $toggle		= 'ldapwins = 1' ;
			$message	= 'LDAP wins on conflict';
			break;
		}


		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__ldapd_mapping'
		. ' SET ' . $toggle
		. ' WHERE mid IN ( '. $cids.'  )'
		. ' AND ( checked_out = 0 OR ( checked_out = ' .(int) $user->get('id'). ' ) )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $db->getError() );
		}
		$this->setMessage( $message );
	}

}

