<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new LDAPDirController();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();
