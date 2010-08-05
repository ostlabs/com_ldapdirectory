<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class LDAPDirControllerGroups extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );
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

		case "display":
		    $this->display();
		    break;

                case "apply":
                case "save":
                    $this->save();
                    break;

                case "edit":
                case "newItem":
                    $this->edit();
                    break;

		case "unpublish":
		case "publish":
		    $this->toggle($task);
		    break;

		case "cancel":
		    $this->cancel();
		    break;

		case "remove":
		    $this->remove();
		    break;

	    }


	}

	function display()
	{
		global $mainframe;

                $model  =& $this->getModel( 'groups' );
		$view =& $this->getView( 'groups' );
                $view->setModel( $model, true );
		$view->display();
	}

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


		$row =& JTable::getInstance('groups', 'Table');
		$row->load( (int) $cid[0] );

		// fail if checked out not by 'me'
		if ($row->isCheckedOut( $userId )) {
			$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups' );
			return JError::raiseWarning( JText::sprintf( 'WARNEDITEDBYPERSON', $row->name ) );
		}

		if ($row->id) {
			// do stuff for existing record
			$row->checkout( $userId );
		} else {
			// do stuff for new record
			$row->published = 0;
		}

		$view =& $this->getView( 'groups' );
		$view->edit($row);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups' );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('groups', 'Table');

		$post           = JRequest::get( 'post' );
		$post['description'] = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );

		if (!$table->bind( $post )) {
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
				$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups&task=edit&cid[]='. $table->id );
				break;
		}

		$this->setMessage( JText::_( 'Item Saved' ) );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$table		=& JTable::getInstance('mappings', 'Table');
		$table->id	= JRequest::getVar( 'mid', 0, 'post', 'int' );
		$table->checkin();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups' );

		$db		=& JFactory::getDBO();
		$mid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		// Initialize variables
		$table	=& JTable::getInstance('groups', 'Table');
		$n		= count( $mid );

		for ($i = 0; $i < $n; $i++)
		{
		    if ($mid[$i] == 1 || $mid[$i] == 2) {
            		return JError::raiseWarning( 500, "You may not delete Static mappings for GroupID or Picture as these are hard coded mappings" );
		    } else {
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

		$this->setRedirect( 'index.php?option=com_ldapdirectory&controller=groups' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$n			= count( $cid );

		switch ($task) {
		    case "publish":
		        $toggle		= 'published = 1' ;
			$message	= 'Item(s) Published';
			break;
		    case "unpublish":
		        $toggle		= 'published = 0' ;
			$message	= 'Item(s) Unpublished';
			break;
		}


		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__ldapd_groups'
		. ' SET ' . $toggle
		. ' WHERE id IN ( '. $cids.'  )'
		. ' AND ( checked_out = 0 OR ( checked_out = ' .(int) $user->get('id'). ' ) )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $db->getError() );
		}
		$this->setMessage( $message );
	}

}

