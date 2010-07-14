<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

// $this->user export details:
// - id, name, username, email, usertype, block, sendEmail, registerDate, lastvisitDate and URL
// - all mapped items are available under $this->user->mapped
// - "link" which is a link to the users profile
// - "cemail" which is the users masked email

// *********************************************************************************************************************
// * REMEMBER ** IMPORANT ** Only some items are editable by the user - this includes mapped items marked as editable! *
// *********************************************************************************************************************

// Example bellow on how to use this view.
// To overload copy this file to webroot/templates/[Template]/html/com_ldapdirectory/user/default.php

// If you do not want to show blocked users, look for $this->user->blocked

?>
<script language="javascript" type="text/javascript">
<!--
        function submitbutton(pressbutton) {
            var form = document.usereditForm;

            // do field validation
            form.submit();
        }
-->
</script>

This will display all the avilable details of the requested user.<BR>
<BR>
<form action="<?php echo JURI::base() ?>index.php" name="usereditForm" method="post" enctype="multipart/form-data">
<?php

    if (is_array($this->user->mdata)) {

	if (sizeof($this->user->mdata['picture']['data']) > 0) {
	    // Put a link to the image
	    echo "<img src='" . JURI::base() . "index.php?option=com_ldapdirectory&task=uimage&uid=" . $this->user->id . "' /><BR><BR>";
	} else {
	    // Default Image
	    echo "<img src='" . JURI::base() . "components/com_ldapdirectory/assets/default.png' /><BR><BR>";
	}

	echo "Upload Image: <input type='file' name='image'><BR><BR>";

	foreach ($this->user->mdata as $data) {
	    if ($data['mid'] == 1 || $data['mid'] == 2) {
		// Group or Picture - Do nothing as this should ALWAYS come from LDAP or placed in a space
	    } elseif ($data['usereditable']) {
		echo $data['displayname'] . ": <input name='data[" . $data['mid'] . "]' value='" . $data['data'] . "'><BR>";
	    } else  {
		echo $data['displayname'] . ": " . $data['data'] . "<BR>";
	    }
	}
    }

?>
        <button class="button" onclick="return submitbutton('send');">
                <?php echo JText::_('SEND'); ?>
        </button>
        <button class="button" onclick="window.close();return false;">
                <?php echo JText::_('CANCEL'); ?>
        </button>
        <input type="hidden" name="uid" value="<?php echo $this->user->id; ?>" />
        <input type="hidden" name="option" value="com_ldapdirectory" />
        <input type="hidden" name="task" value="save" />
        <?php echo JHTML::_( 'form.token' ); ?>
</form>
