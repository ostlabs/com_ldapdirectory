<?

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package              Joomla
* @subpackage   Banners
*/
class LDAPDirViewUsers
{

       function displayusers( &$rows, &$pageNav, &$lists )
        {
		JToolBarHelper::title( JText::_( 'Manage Users' ), 'generic.png' );
                JToolBarHelper::deleteList( '', 'remove' );
                JToolBarHelper::editListX( 'edit' );
		ldapdirHelper::basicicons(true);
                $user =& JFactory::getUser();
                JHTML::_('behavior.tooltip');
    		$imgY = 'tick.png'; $imgX = 'publish_x.png';
                ?>
                <form action="index.php" method="post" name="adminForm">

                        <table>
                        <tr>
                                <td align="left" width="100%">
                                        <?php echo JText::_( 'Filter' ); ?>:
                                        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
                                        <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                                        <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
                                </td>
                                <td nowrap="nowrap">
                                </td>
                        </tr>
                        </table>

                        <table class="adminlist">
                        <thead>
                        <tr>
                                <th width="20">
                                        <?php echo JText::_( 'Num' ); ?>
                                </th>
                                <th width="20">
                                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                                </th>
                                <th nowrap="nowrap" class="title">
                                        <?php echo JHTML::_('grid.sort',   'Name', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th nowrap="nowrap" class="title" width="35%">
                                        <?php echo JHTML::_('grid.sort',   'User Name', 'a.username', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th align="center" nowrap="nowrap" width="35%">
                                        <?php echo JHTML::_('grid.sort',   'Email', 'a.email', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th width="1%" nowrap="nowrap">
                                        <?php echo JHTML::_('grid.sort',   'Mappings', 'datacount', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th width="1%" nowrap="nowrap">
                                        <?php echo JHTML::_('grid.sort',   'ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                                <td colspan="9">
                                        <?php echo $pageNav->getListFooter(); ?>
                                </td>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        $k = 0;
                        for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                                $row = &$rows[$i];

                                $link                   = JRoute::_( 'index.php?option=com_ldapdirectory&controller=users&task=edit&cid[]='. $row->id );

                                $checked                = JHTML::_('grid.checkedout',   $row, $i );
                                ?>
                                <tr class="<?php echo "row$k"; ?>">
                                        <td align="center">
                                                <?php echo $pageNav->getRowOffset( $i ); ?>
                                        </td>
                                        <td>
                                                <?php echo $checked; ?>
                                        </td>
                                        <td>
                                                <?php
                                                if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
                                                        echo $row->name;
                                                } else {
                                                        ?>
                                                                <span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->name; ?>">
                                                        <a href="<?php echo $link; ?>">
                                                                <?php echo $row->name; ?></a>
                                                                </span>
                                                        <?php
                                                }
                                                ?>
                                        </td>
                                        <td>
                                                <?php echo $row->username; ?>
                                        </td>
                                        <td align="center">
                                                <?php echo $row->email;?>
                                        </td>
                                        <td align="center">
                                                <?php echo $row->datacount; ?>
                                        </td>
                                        <td align="center">
                                                <?php echo $row->id; ?>
                                        </td>
                                </tr>
                                <?php
                                $k = 1 - $k;
                        }
                        ?>
                        </tbody>
                        </table>

                <input type="hidden" name="option" value="com_ldapdirectory" />
		<input type="hidden" name="controller" value="users" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
                <?php echo JHTML::_( 'form.token' ); ?>
                </form>
                <?php
        }

	function setClientToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');

		JToolBarHelper::title( $task == 'add' ? JText::_( 'Users' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'Users' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel( 'cancel' );
	}


	function edit( &$row, $mappings )
	{
		global $mainframe;
		LDAPDirViewUsers::setClientToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );

		$yesno[] = JHTML::_( 'select.option', '1', 'Yes' );
		$yesno[] = JHTML::_( 'select.option', '0', 'No' ); // first parameter is value, second is text

		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel')
			{
				submitform( pressbutton );
				return;
			}
			// do field validation
//			if (form.name.value == "")
//			{
//				alert( "<?php echo JText::_( 'Please fill in the Mapping Name.', true ); ?>" );
//			}
//			else if (form.displayname.value == "")
//			{
//				alert( "<?php echo JText::_( 'Please fill in the Display Name.', true ); ?>" );
//			}
//			else
//			{
				submitform( pressbutton );
//			}
		}
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">

		<div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Details' ); ?></legend>

				<table class="admintable">
					<tr>
						<td width="20%" nowrap="nowrap">
							<label for="name">
								<?php echo JText::_( 'Full Name' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="userd[name]" id="userd[name]" size="40" maxlength="60" value="<?php echo $row['name']->data; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<label for="contact">
								<?php echo JText::_( 'User Name' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="userd[username]" id="userd[username]" size="40" maxlength="60" value="<?php echo $row['username']->data; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<label for="email">
								<?php echo JText::_( 'eMail' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="userd[email]" id="userd[email]" size="40" maxlength="60" value="<?php echo $row['email']->data; ?>" />
						</td>
					</tr>
					</table>
			</fieldset>
		</div>

		<div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Users Image' ); ?></legend>
				<?php
		    	    if (sizeof($row['picture']->data) > 0) {
			        // Put a link to the image
			        echo "<img src='" . $mainframe->getSiteURL() . "index.php?option=com_ldapdirectory&task=uimage&uid=" . $row['id'] . "' /><BR><BR>";
				echo "Keep Users Image:" . JHTML::_('select.radiolist', $yesno, 'kimage', 'class="inputbox" ', 'value', 'text', '1', 'align' );
			    } else {
			        // Default Image
			        echo "<img src='" . $mainframe->getSiteURL() . "components/com_ldapdirectory/assets/default.png' /><BR><BR>";
			    }
			    ?>

			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Mapped Info' ); ?></legend>

				<table class="admintable" width="100%">
				<?php foreach ($mappings as $value) { ?>
				<?php if ($value['mid'] != 1 && $value['mid'] !=2) { ?>
				<tr>
    					<td nowrap="nowrap">
						<label for="email">
							<?php echo $value['displayname']; ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="mapping[<?php echo $value['mid']; ?>]" id="mapping[<?php echo $value['name']; ?>]" size="40" maxlength="60" value="<?php echo $row[$value['name']]->data; ?>" />
					</td>
				</tr>
				<?php } } ?>
				</table>
			</fieldset>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="controller" value="users" />
		<input type="hidden" name="option" value="com_ldapdirectory" />
		<input type="hidden" name="cid" value="<?php echo $row['id']; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}

