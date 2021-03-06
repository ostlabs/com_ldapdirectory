<?php
/**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class ldapdirViewgroups extends JView
{
        function setClientToolbar()
        {
                $task = JRequest::getVar( 'task', '', 'method', 'string');

                JToolBarHelper::title( $task == 'add' ? JText::_( 'Mappings' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'Mappings' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
                JToolBarHelper::save( 'save' );
                JToolBarHelper::apply('apply');
                JToolBarHelper::cancel( 'cancel' );
        }

	function display($tpl=null)
	{
		global $mainframe;

		$this->_layout = 'default';

		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::title( JText::_( 'Group Manager' ), 'menu.png' );

		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList( '', 'remove' );
		JToolBarHelper::addNewX('newItem');
		ldapdirHelper::basicicons(true);

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('View Menu Items'));


		$limitstart = JRequest::getVar('limitstart', '0', '', 'int');
		$items		= &$this->get('Items');
		$pagination	= &$this->get('Pagination');
		$lists		= &$this->_getViewLists();
		$user		= &JFactory::getUser();

		// Ensure ampersands and double quotes are encoded in item titles
		foreach ($items as $i => $item) {
			$treename = $item->treename;
			$treename = JFilterOutput::ampReplace($treename);
			$treename = str_replace('"', '&quot;', $treename);
			$items[$i]->treename = $treename;
		}

		//Ordering allowed ?
		$ordering = ($lists['order'] == 'm.ordering');

		JHTML::_('behavior.tooltip');

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		$this->assignRef('user', $user);
		$this->assignRef('menutype', $menutype);
		$this->assignRef('ordering', $ordering);
		$this->assignRef('limitstart', $limitstart);

		parent::display($tpl);
	}

	function edit($row)
	{
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ldapdirectory'.DS.'helpers'.DS.'helper.php' );
		$this->setClientToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );

		$yesno[] = JHTML::_( 'select.option', '1', 'Yes' );
		$yesno[] = JHTML::_( 'select.option', '0', 'No' ); // first parameter is value, second is text
		$editor = &JFactory::getEditor();
		echo $editor->initialise();
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
				alert( "<?php echo JText::_( 'Please fill in the Name.', true ); ?>" );
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
						<td width="20%" nowrap="nowrap" class="key" align="right" valign="top>
							<label for="name">
								<?php echo JText::_( 'Name' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $row->name; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="key" align="right" valign="top>
							<label for="ldapvalue">
								<?php echo JText::_( 'LDAP Value' ); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="lvalue" id="lvalue" size="40" maxlength="60" value="<?php echo $row->lvalue; ?>" />
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="key" align="right" valign="top">
							<label for="enabled">
								<?php echo JText::_( 'Enabled' ); ?>:
							</label>
						</td>
						<td>
							<? echo JHTML::_('select.radiolist', $yesno, 'published', 'class="inputbox" ', 'value', 'text', $row->published, 'align' ); ?>
						</td>
					</tr>
                                                <tr>
                                                        <td class="key" align="right" valign="top">
                                                                <?php echo JText::_( 'Parent Item' ); ?>:
                                                        </td>
                                                        <td>
                                                                <?php echo LDAPDirHelperView::Parent( $row ); ?>
                                                        </td>
                                                </tr>
					</tr>
					</table>
					<BR>
					<h3>Description:</h3>
					 <?php echo $editor->display("description", $row->description, 1000, 400, 1000, 100, false); ?>
			</fieldset>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="controller" value="groups" />
		<input type="hidden" name="option" value="com_ldapdirectory" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="client_id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php

	}

	function &_getViewLists()
	{
		global $mainframe;
		$db		=& JFactory::getDBO();

		$menutype			= $mainframe->getUserStateFromRequest( "com_ldapdirectory.menutype",					'menutype',			'mainmenu',		'string' );
		$filter_order		= $mainframe->getUserStateFromRequest( "com_ldapdirectory.$menutype.filter_order",		'filter_order',		'm.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_ldapdirectory.$menutype.filter_order_Dir",	'filter_order_Dir',	'ASC',			'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( "com_ldapdirectory.$menutype.filter_state",		'filter_state',		'',				'word' );
		$levellimit			= $mainframe->getUserStateFromRequest( "com_ldapdirectory.$menutype.levellimit",		'levellimit',		10,				'int' );
		$search				= $mainframe->getUserStateFromRequest( "com_ldapdirectory.$menutype.search",			'search',			'',				'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		// level limit filter
		$lists['levellist'] = JHTML::_('select.integerlist',    1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit );

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		return $lists;
	}
}

