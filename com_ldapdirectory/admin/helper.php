<?
// no direct access
defined('_JEXEC') or die('Restricted access');

class ldapdirHelper
{

    function basicicons($hasform=false)
    {
        JToolBarHelper::customX( 'sync', 'copy.png', 'copy_f2.png', 'Sync Users', false );
        JToolBarHelper::preferences('com_ldapdirectory', '500');
	if (!$hasform)
	{
        echo '<form action="index.php" method="post" name="adminForm" autocomplete="off">';
        echo '<input type="hidden" name="option" value="'.$option.'com_ldapdirectory" /><input type="hidden" name="task" value="'.$task.'" />';
        echo '</form>';
	}
    }

    function syncer() {
	jimport('joomla.client.ldap');
        jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
        jimport('joomla.user.helper');

	$dbo =& JFactory::getDBO();
        $acl =& JFactory::getACL();

	$params = &JComponentHelper::getParams( 'com_ldapdirectory' );
        $usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params
	$row =& JTable::getInstance('users', 'Table');

        // get the default usertype
        $usertype = $usersParams->get( 'new_usertype' );
        if (!$usertype) {
            $usertype = 'Registered';
        }

	if ($params->get('use_ldapauth'))
	{
	    $dbo->setQuery('SELECT params FROM #__plugins WHERE folder = "authentication" AND element = "ldap"');
	    $paramstring = $dbo->loadResult() or die($dbo->getErrorMsg());
	    if(!$paramstring) die('Failed to get a param string!');
	    $ldapparams = new JParameter($paramstring);
	} else {
	    // TODO: Write code to use internal LDAP settings not relying on LDAPAuth Settings
	}

	$ldap = new JLDAP($ldapparams);

	if(!$ldap->connect()) {
	    echo '<p>Failed to connect to LDAP server: '. $ldap->getErrorMsg() . '</p>';
	    return false;
	}

	if(!$ldap->bind()) {
	    echo '<p>Failed to bind to LDAP server: '. $ldap->getErrorMsg() .'</p>';
	    return false;
	}

	$results = $ldap->simple_search($params->get('ldap_filterstring'));

        $query = 'SELECT a.*'
        . ' FROM #__ldapd_mapping AS a'
        . ' WHERE a.fromldap = 1'
        ;

        $dbo->setQuery( $query );
	$rows = $dbo->loadObjectList();

	foreach ($results as $value) {

	    $commit = $params->get('use_commit');
	    $username = $value[$params->get('ldap_uid')][0];
	    $fullname = $value[$params->get('ldap_fullname')][0];
	    $email = $value[$params->get('ldap_email')][0];

	    echo "User DN: " . $value['dn'] . "<BR>";
	    echo "<UL>";

	    if ($username == "" || $fullname == "" || $email == "" )
	    {
		echo "	<LI><font color='RED'>Critical Details Missing: NOT saving.</font></li>";
		$commit = false;
	    }
	    elseif (!$commit)
		echo "	<LI><font color='GREEN'>In No Commit Mode: NOT saving.</font></li>";
	    else
	    {

                if (!$id = JUserHelper::getUserId($username)) {
		    $id = 0;
		}

		// Bug in 1.5 causing this to not work: $user = &JFactory::getUser($id); so use the following instead
		$user = new JUser($id);

    		$data = array(); // array for all user settings
		$data['username'] = $username;
		$data['name'] = $fullname;
	        $data['email'] = $email;
		if ($id == 0) {
		    $data['gid'] = $acl->get_group_id( '', $usertype, 'ARO' );  // generate the gid from the usertype
		    $data['sendEmail'] = 0;
		}
		$data['block'] = 0;

		if (!$user->bind($data)) {
		    // Something went wrong
		    echo "PROBLEM1";
		}

		if (!$user->save()) {
		    // Something else went wrong
		    echo "PROBLEM2";
		}
	    }

	    echo "	<LI>User Name: " . $username . "</li>";
	    echo "	<LI>Full Name: " . $fullname . "</li>";
	    echo "	<LI>email: " . $email . "</li>";
	    foreach ($rows as $mapping) {
		echo "	<LI>" . $mapping->displayname . " ( " . $mapping->name . " ): " . $value[$mapping->ldapfield][0] . "</LI>";
		if ($commit && $value[$mapping->ldapfield][0] != "")
		{
		    // Insert into data DB
		    $row->storedata ($user->id, $mapping->mid, $value[$mapping->ldapfield][0], $mapping->ldapwins);
		}
	    }
	    echo "</UL>";
	}

    }

}