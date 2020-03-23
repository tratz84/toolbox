<?php
use core\forms\SelectField;
?>


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=calendar&c=view') ?>" class="fa fa-calendar"></a>
	</div>

	<h1><?= t('Overview open calendar items')?></h1>
</div>



<div>

	<table class="tbl-calendar-items list-response-table">
		<thead>
			<tr>
				<th style="width: 25px;">
					<input type="checkbox" class="top-select-item-action" />
				</th>
				<th style="width: 125px;">Date</th>
				<th style="width: 125px;">Time</th>
				<th>Description</th>
				<th width="200">Status</th>
			</tr>
		</thead>
	
		<tbody>
			<?php foreach($events as $e) : ?>
			<tr class="calendar-item"
    				data-calendar-item-id="<?= $e->getId() ?>"
    				data-recurrent="<?= $e->getRecurrent() ? 'true':'false' ?>"
    				data-start-date="<?= $e->getStartDate() ?>">
				<td>
					<input type="checkbox" class="select-item-action" />
				</td>
				<td><?= $e->getStartDateFormat('d-m-Y') ?></td>
				<td><?= $e->getStartTime() ?></td>
				<td><?= esc_html($e->getDescription()) ?></td>
				<td class="item-action">
					<?php 
					   $sl = new SelectField('item-action', $e->getItemAction(), \calendar\model\CalendarItem::getItemActions(), '');
					   print $sl->render();
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<div>
		Actie:
		<select name="action_name">
			<option value="change_item_action">Status zetten naar</option>
		</select>
		<select name="action_item_name">
			<option value=""><?= t('Make your choice') ?></option>
			<?php foreach(\calendar\model\CalendarItem::getItemActions() as $key => $val) : ?>
			<option value="<?= esc_attr($key) ?>"><?= esc_html($val) ?></option>
			<?php endforeach; ?>
		</select>
		
		<input type="button" value="<?= t('Execute') ?>" onclick="btnExecute_Click();" />
	</div>
</div>



<script>

$(document).ready(function() {
	$('.tbl-calendar-items tr.calendar-item').each(function(index, node) {
		$(node).find('td.item-action select').change(function() {
			var tr = $(this).closest('tr');

			update_itemAction( tr, $(this).val() );
		});
	});

	$('.top-select-item-action').change(function() {
		var checked = $(this).prop('checked');

		$('.select-item-action').prop('checked', checked);
	});

	$('.select-item-action').change(function() {
		var total_checkboxes = $('.select-item-action').length;
		var checked_checkboxes = $('.select-item-action:checked').length;

		if (total_checkboxes == checked_checkboxes) {
			$('.top-select-item-action').prop('checked', true);
		} else {
			$('.top-select-item-action').prop('checked', false);
		}
	});
	
});


function btnExecute_Click() {

	var trs = new Array();
	
	$('.select-item-action:checked').each(function(index, node) {
		trs.push( $(node).closest('tr') );
	});
	
	// items selected?
	if (trs.length == 0) {
		showAlert('Error', 'No items selected');
		return false;
	}
	
	// action selected?
	if ($('[name=action_item_name]').val() == '') {
		showAlert('Error', 'No action selected');
		return false;
	}

	
	update_itemAction( $(trs), $('[name=action_item_name]').val() );
}



function update_itemAction( rowSelector, itemAction ) {

	var data = {};

	rowSelector.each(function(index, node) {
		var ci_id = $(node).data('calendar-item-id');
		var ci_start_date = $(node).data('start-date');

		data['calendar_item[' + index + '][id]']          = ci_id;
		data['calendar_item[' + index + '][start_date]']  = ci_start_date;
		data['calendar_item[' + index + '][item_action]'] = itemAction;
	});

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=calendar&c=calitemActionOverview&a=update_item_action'),
		data: data,
		success: function(data, textStatus, xhr) {
			show_user_message('Changes saved');
			window.location = appUrl('/?m=calendar&c=calitemActionOverview');
		},
		error: function(xhr, textStatus, errorThrown) {
			alert('Error: ' + xhr.responseText);
		}
	});
}



</script>




