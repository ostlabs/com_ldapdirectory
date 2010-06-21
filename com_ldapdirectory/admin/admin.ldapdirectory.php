<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.DS.'helper.php' );
//require_once (JApplicationHelper::getPath('admin_html'));

// Set the helper directory
JHTML::addIncludePath( JPATH_COMPONENT.DS.'helper' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'tables');

$controller = JRequest::getCmd('controller');

$task = JRequest::getCmd('task');

if ($task == "sync") $controller = "sync";

switch (strtolower($controller))
{
	default :
	case 'mappings' :
	    require_once(JPATH_COMPONENT.DS.'controllers'.DS.'mappings.php');
	    $controller = new LDAPDirControllerMappings();
    	    $controller->handletask($task);
	    $controller->redirect();
	    break;

	case 'users' :
	    require_once(JPATH_COMPONENT.DS.'controllers'.DS.'users.php');
	    $controller = new LDAPDirControllerUsers();
    	    $controller->handletask($task);
	    $controller->redirect();
	    break;

	case 'sync' :
	    JToolBarHelper::title( JText::_( 'Syncing Users' ), 'generic.png' );
	    ldapdirHelper::basicicons();
	    echo "Syncing LDAP Users.<BR><BR>";
	    ldapdirHelper::syncer();
	    break;

}