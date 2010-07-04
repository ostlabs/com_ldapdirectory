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
class Tableusers extends JTable
{

	var $id = 0;
	var $uid = 0;
	var $mid = 0;
	var $data = "";
	var $blobdata = null;

	function __construct( &$_db )
	{
		parent::__construct( '#__ldapd_userdata', 'id', $_db );

	}

	function getuser($id)
	{
	    $user = JFactory::getUser($id);

	    $data['id'] = $id;
	    $data['name']->data = $user->name;
	    $data['username']->data = $user->username;
	    $data['email']->data = $user->email;

	    $query = "select m.mid, m.name, m.displayname, data"
		     . " from #__ldapd_userdata as d"
		     . " LEFT JOIN #__ldapd_mapping as m ON m.mid = d.mid "
		     . " where uid = $id";
	    $this->_db->setQuery($query);
	    $cdata = $this->_db->loadAssocList();

	    foreach ($cdata as $value)
	    {
		$data[$value['name']]->dname = $value['displayname'];
		$data[$value['name']]->data = $value['data'];
	    }

	    return $data;

	}

	function storedata($uid, $mid, $data, $overwrite = 0, $blobdata = null)
	{
	    $query = "SELECT id FROM #__ldapd_userdata AS d WHERE uid = $uid AND mid = $mid";
	    $this->_db->setQuery($query);
	    if ($id = $this->_db->loadResult())
		$this->id = $id;
	    else
		$this->id = null;
	    $this->uid = (int) $uid;
	    $this->mid = (int) $mid;
	    $this->data = filter_var($data, FILTER_SANITIZE_STRING);
	    $this->blobdata = $blobdata;
	    if ($id == null  || $overwrite)
		$this->store();
	}

	function storeimage ($uid, $image)
	{
	    // verify this is an image, other wise return NULL
	    $size = getimagesize ($image['tmp_name']);
	    if ($size && $size[0] < 200 && $size[1] < 200) {
		$fp = fopen($image['tmp_name'], "rb");
		$this->storedata($uid, 2, $size['mime'], 1, fread($fp, $image['size']));
	    }
	    return null;
	}

	function deleteimage($uid)
	{
	    $query = "SELECT id FROM #__ldapd_userdata AS d WHERE uid = $uid AND mid = 2";
	    $this->_db->setQuery($query);
	    if ($id = $this->_db->loadResult())
	    $this->delete($id);
	}
}
