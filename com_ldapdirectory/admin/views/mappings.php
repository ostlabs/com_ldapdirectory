<?

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package              Joomla
* @subpackage   Banners
*/
class LDAPDirViewMappings
{

       function mappings( &$rows, &$pageNav, &$lists )
        {
		JToolBarHelper::title( JText::_( 'Manage Field Mappings' ), 'generic.png' );
                JToolBarHelper::deleteList( '', 'remove' );
                JToolBarHelper::editListX( 'edit' );
                JToolBarHelper::addNewX( 'add' );
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
                                        <?php echo JHTML::_('grid.sort',   'Display Name', 'a.displayname', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th align="center" nowrap="nowrap" width="5%">
                                        <?php echo JHTML::_('grid.sort',   'User Editable', 'a.usereditable', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th align="center" nowrap="nowrap" width="5%">
                                        <?php echo JHTML::_('grid.sort',   'LDAP Based', 'a.fromldap', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th align="center" nowrap="nowrap" width="35%">
                                        <?php echo JHTML::_('grid.sort',   'LDAP Map', 'a.ldapfield', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th align="center" nowrap="nowrap" width="5%">
                                        <?php echo JHTML::_('grid.sort',   'LDAP Wins', 'a.ldapwins', @$lists['order_Dir'], @$lists['order'] ); ?>
                                </th>
                                <th width="1%" nowrap="nowrap">
                                        <?php echo JHTML::_('grid.sort',   'ID', 'a.mid', @$lists['order_Dir'], @$lists['order'] ); ?>
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

                                $row->id                = $row->mid;
                                $link                   = JRoute::_( 'index.php?option=com_ldapdirectory&controller=mapping&task=edit&cid[]='. $row->id );

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
                                                <?php echo $row->displayname; ?>
                                        </td>
                                        <td align="center">
					    <?php
            				    $img    = $row->usereditable ? $imgY : $imgX;
			                    $task   = $row->usereditable ? 'editable' : 'noteditable';
			                    $alt    = $row->usereditable ? JText::_( 'User editable' ) : JText::_( 'User not able to edit' );
			                    $action = $row->usereditable ? JText::_( 'Make Uneditable' ) : JText::_( 'Make editable' );

			                    echo '
			                    <a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
			                    <img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
			                    ;
					    ?>
                                        </td>
                                        <td align="center">
					    <?php
            				    $img    = $row->fromldap ? $imgY : $imgX;

			                    echo '<img src="images/'. $img .'" border="0" alt="'. $alt .'" />';
					    ?>
                                        </td>
                                        <td align="center">
                                                <?php echo $row->ldapfield;?>
                                        </td>
                                        <td align="center">
					    <?php
            				    $img    = $row->ldapwins ? $imgY : $imgX;
			                    $task   = $row->ldapwins ? 'ldapwins' : 'ldaplooses';
			                    $alt    = $row->ldapwins ? JText::_( 'LDAP Wins' ) : JText::_( 'LDAP Looses' );
			                    $action = $row->ldapwins ? JText::_( 'LDAP to loose' ) : JText::_( 'LDAP to win' );

			                    echo '
			                    <a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
			                    <img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
			                    ;
					    ?>
                                        </td>
                                        <td align="center">
                                                <?php echo $row->mid; ?>
                                        </td>
                                </tr>
                                <?php
                                $k = 1 - $k;
                        }
                        ?>
                        </tbody>
                        </table>

                <input type="hidden" name="option" value="com_ldapdirectory" />
		<input type="hidden" name="controller" value="mappings" />
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

		JToolBarHelper::title( $task == 'add' ? JText::_( 'Mappings' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'Mappings' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel( 'cancel' );
	}


	function mapping( &$row )
	{
		LDAPDirViewMappings::setClientToolbar();
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
			if (form.name.value == "")
			{
				alert( "<?php echo JText::_( 'Please fill in the Mapping Name.', true ); ?>" );
			}
			else if (form.displayname.value == "")
			{
				alert( "<?php echo JText::_( 'Please fill in the Display Name.', true ); ?>" );
			}
			else
			{
				submitform( pressbutton );
			}
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
								<?php echo JText::_( 'Name' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $row->name; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<label for="contact">
								<?php echo JText::_( 'Display Name' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="displayname" id="displayname" size="40" maxlength="60" value="<?php echo $row->displayname; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<label for="email">
								<?php echo JText::_( 'User Editable' ); ?>:
							</label>
						</td>
						<td>
							<? echo JHTML::_('select.radiolist', $yesno, 'usereditable', 'class="inputbox" ', 'value', 'text', $row->usereditable, 'align' ); ?>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">
							<label for="email">
								<?php echo JText::_( 'Get details from LDAP' ); ?>:
							</label>
						</td>
						<td>
							<? echo JHTML::_('select.radiolist', $yesno, 'fromldap', 'class="inputbox" ', 'value', 'text', $row->fromldap, 'align' ); ?>
						</td>
					</tr>
					</table>
			</fieldset>
		</div>

		<div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'LDAP Info' ); ?></legend>

				<table class="admintable" width="100%">
				<tr>
    					<td nowrap="nowrap">
						<label for="email">
							<?php echo JText::_( 'LDAP Field' ); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="ldapfield" id="ldapfield" size="40" maxlength="60" value="<?php echo $row->ldapfield; ?>" />
					</td>
				</tr>
				<tr>
    					<td nowrap="nowrap">
						<label for="email">
							<?php echo JText::_( 'LDAP Wins on Duplicate' ); ?>:
						</label>
					</td>
					<td>
						<? echo JHTML::_('select.radiolist', $yesno, 'ldapwins', 'class="inputbox" ', 'value', 'text', $row->ldapwins, 'align' ); ?>
					</td>
				</tr>
				</table>
			</fieldset>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="controller" value="mappings" />
		<input type="hidden" name="option" value="com_ldapdirectory" />
		<input type="hidden" name="mid" value="<?php echo $row->mid; ?>" />
		<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}

