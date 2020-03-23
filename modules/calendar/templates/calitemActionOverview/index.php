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
		
		<input type="button" value="<?= t('Execute') ?>" />
	</div>
</div>



<script>

$(document).ready(function() {
	$('.tbl-calendar-items tr.calendar-item').each(function(index, node) {
		$(node).find('td.item-action select').change(function() {
			
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




</script>




