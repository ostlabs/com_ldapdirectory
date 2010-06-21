<?php
/**
 * @version		$Id: controller.php 14974 2010-02-21 14:32:22Z ian $
 * @package		Joomla
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'query.php');


/**
 * Contact Component Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	Contact
 * @since 1.5
 */
class LDAPDirController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$document =& JFactory::getDocument();

		$viewName	= JRequest::getVar('view', 'groups', 'default', 'cmd');
		$viewType	= $document->getType();

		// Set the default view name from the Request
		$view = &$this->getView($viewName, $viewType);

		// Push a model into the view
		$model	= &$this->getModel( $viewName );
		if (!JError::isError( $model )) {
			$view->setModel( $model, true );
		}

		// Display the view
		$view->assign('error', $this->getError());
		
		$view->display();
	}

	function save() {

	    JRequest::checkToken() or jexit( 'Invalid Token' );

	    $data = JRequest::getVar('data', array(), 'default', 'array');
	    $image = JRequest::getVar('image', array(), 'FILES', 'array');
	    $uid = JRequest::getVar('uid', '0', 'default', 'int');
	    // Check that uid = self
	    $myid = &JFactory::getUser();
	    if ($myid->id != $uid) {
		// Error out
		echo "I'm sorry dave, I can't do that";
	    } else {
		// get the array of DATA
		foreach ($data as $mid => $value ) {
		    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'tables');
		    $table  =& JTable::getInstance('users', 'Table');
		    $table->storedata($myid->id, $mid, $value, 1);
		}
		if ($image['error'] == 0) {
		    // Save the image
		    $table->storeimage($myid->id, $image);
		}
    		echo "Saved";
	    }
	}

	function sync() {
	    require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'helper.php');
	    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'tables');

	    $config =& JFactory::getConfig();
	    $key = JRequest::getCmd( 'key' );

            $document = &JFactory::getDocument();                                                                                                                                                                                    
    	    $doc = &JDocument::getInstance('raw');                                                                                                                                                                                   
    	    // Swap the objects                                                                                                                                                                                                      
    	    $document = $doc;

	    if ($key == $config->getValue( 'config.secret' ))
		ldapdirHelper::syncer();
	    else
		echo "Incorrect Key";
	}

	function uimage() {

	    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'tables');

	    $uid = JRequest::getInt( 'uid' );

            $document = &JFactory::getDocument();                                                                                                                                                                                    
            $doc = &JDocument::getInstance('raw');                                                                                                                                                                                   
            // Swap the objects                                                                                                                                                                                                      
            $document = $doc;

	    $image = LDAPDirHelperQuery::getuserimage($uid);
	    $document->setMimeEncoding($image->data);

	    echo $image->blobdata;

	}

}