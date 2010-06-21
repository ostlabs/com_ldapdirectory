<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

// $this->user export details:
// id, name, username, email, usertype, block, sendEmail, registerDate, lastvisitDate and URL
// URL is a routed url to the specific user view
// Example bellow on how to use this view.
// To overload copy this file to webroot/templates/[Template]/html/com_ldapdirectory/users/default.php

// If you do not want to show blocked users, look for $this->users->blocked

?>

This will display all users in a list.<BR>
<BR>
<table>
<tr><td>Name</td><td>email</td></tr>
<?php  foreach ($this->users as $user) { ?>
    <tr>
	<td><a href="<?php echo $user->link; ?>"><?php echo $user->name; ?></a></td>
	<td><?php echo $user->cemail; ?></td>
    </tr>
<? } ?>
</table>

