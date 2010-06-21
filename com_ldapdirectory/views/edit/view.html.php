<?php
/**
 * @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
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

jimport('joomla.application.component.view');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class ldapdirViewEdit extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		// Show all the groups
		
		$myid = &JFactory::getUser();

		if ($myid->id == 0) {
		    // through error as they are not logged int
		    echo "I'm sorry dave, I can't let you do that";
		} else {

                    $model =& $this->getModel();                                                                                                                                                                                         
	            $user = $model->getUser($myid->id); 
    	    	    $this->assignRef('user', $user);
    
		    parent::display($tpl);
		}

	}
}