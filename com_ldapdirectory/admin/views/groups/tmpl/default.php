<?php
 /**
 * @package LDAPDirectory for Joomla! 1.5
 * @author Jason Kendall
 * @copyright (C) 2010 - OSTLabs Inc
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_ldapdirectory" method="post" name="adminForm">

	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('levellimit').value='10';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
				echo JText::_( 'Max Levels' );
				echo $this->lists['levellist'];
				echo $this->lists['state'];
				?>
			</td>
		</tr>
	</table>

<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',   'Group Name', 'm.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="25%">
				<?php echo JHTML::_('grid.sort',   'LDAP Value', 'm.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Published', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Order by', 'm.ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				<?php if ($this->ordering) echo JHTML::_('grid.order',  $this->items ); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Itemid', 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="12">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	$i = 0;
	$n = count( $this->items );
	$rows = &$this->items;
	foreach ($rows as $row) :
		$checked 	= JHTML::_('grid.checkedout',   $row, $i );
		$published 	= JHTML::_('grid.published', $row, $i );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $i + 1 + $this->pagination->limitstart;?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td nowrap="nowrap">
				<?php if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) : ?>
				<?php echo $row->treename; ?>
				<?php else : ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Menu' );?>::<?php echo $row->treename; ?>">
				<a href="<?php echo JRoute::_( 'index.php?option=com_ldapdirectory&controller=groups&task=edit&cid[]='.$row->id ); ?>"><?php echo $row->treename; ?></a></span>
				<?php endif; ?>
			</td>
			<td>
				<?php echo $row->lvalue; ?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
			<td class="order" nowrap="nowrap">
				<span><?php echo $this->pagination->orderUpIcon( $i, $row->parent == 0 || $row->parent == @$rows[$i-1]->parent, 'orderup', 'Move Up', $this->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, $row->parent == 0 || $row->parent == @$rows[$i+1]->parent, 'orderdown', 'Move Down', $this->ordering ); ?></span>
				<?php $disabled = $this->ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
		$i++;
		?>
	<?php endforeach; ?>
	</tbody>
	</table>

	<input type="hidden" name="option" value="com_ldapdirectory" />
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>