<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

// $this->user export details:
// - id, name, username, email, usertype, block, sendEmail, registerDate, lastvisitDate and URL
// - all mapped items are available under $this->user->mdata
// - "link" which is a link to the users profile
// - "cemail" which is the users masked email

// Example bellow on how to use this view.
// To overload copy this file to webroot/templates/[Template]/html/com_ldapdirectory/user/default.php

// If you do not want to show blocked users, look for $this->user->blocked

?>

This will display all the avilable details of the requested user.<BR>
<BR>
<?php

    // Example to display user's image

    if (sizeof($this->user->mdata['picture']['data']) > 0) {
        // Put a link to the image
        echo "<img src='" . JURI::base() . "index.php?option=com_ldapdirectory&task=uimage&uid=" . $this->user->id . "' /><BR><BR>";
    } else {
        // Default Image
        echo "<img src='" . JURI::base() . "components/com_ldapdirectory/assets/default.png' /><BR><BR>";
    }

    // Example to display all joomla user data

    foreach ($this->user as $m => $user) {
	if (!is_array($user)) echo "$m: $user<BR>";
    }

    // Example to display all mapped data of user

    foreach ($this->user->mdata as $mdata) {
	echo $mdata['displayname'] . ": " . $mdata['data'] . "<BR>";
    }

    // Example to display a specific mapped data

    echo "Users picture type: " . $this->user->mdata['picture']['data'];


