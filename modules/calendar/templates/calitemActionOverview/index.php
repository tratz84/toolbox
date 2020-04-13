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
	
	<div id="calendar-item-table-container"></div>

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


var map_itemActions = <?= json_encode($map_itemActions) ?>;


var it = new IndexTable('#calendar-item-table-container');

it.setRowClick(function(row, evt) {
	// no selection in action-box
	if ($(evt.target).hasClass('td-actions') || $(evt.target).closest('.td-actions').length > 1)
		return;
	if ($(evt.target).hasClass('select-item-action'))
		return;


	var selectItemAction = $(evt.target).closest('tr').find('.select-item-action');
	selectItemAction.prop( 'checked', !selectItemAction.prop('checked') );
	selectItemAction.trigger('change');
	
});

it.setConnectorUrl( '/?m=calendar&c=calitemActionOverview&a=search&calendar_id=<?= $calendar_id ?>' );

it.setCallbackRenderRows(function() {
	$('#calendar-item-table-container tbody tr').each(function(index, node) {
		$(node).find('td.td-actions select').change(function() {
			var tr = $(this).closest('tr');

			update_itemAction( tr, $(this).val() );
		});
	});

	$('.th-topselectitemaction').empty();
	$('.th-topselectitemaction').append('<input type="checkbox" class="top-select-item-action" />');

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


it.addColumn({
	fieldName: 'top-select-item-action',
	width: 25,
	fieldDescription: '',
	fieldType: 'text',
	searchable: false,
	render: function( record ) {
		var chk = $('<input type="checkbox" class="select-item-action" />');

		chk.attr('title', record.calendar_item_id);
		
		return chk;
	}
});

it.addColumn({
	fieldName: 'start_date',
	width: 125,
	fieldDescription: t('Date'),
	fieldType: 'date',
	searchable: false
});
it.addColumn({
	fieldName: 'start_time',
	width: 125,
	fieldDescription: t('Time'),
	fieldType: 'time',
	searchable: false
});
it.addColumn({
	fieldName: 'description',
	fieldDescription: t('Description'),
	fieldType: 'text',
	searchable: false
});
it.addColumn({
	fieldName: 'actions',
	width: 125,
	fieldDescription: '',
	fieldType: 'text',
	searchable: false,
	render: function( row ) {
		var s = $('<select class="item-action" />');

		for(var i in map_itemActions) {
			var opt = $('<option />');
			opt.attr('value', i);
			opt.text( map_itemActions[i] );

			if (i == row.item_action) {
				opt.attr('selected', 'selected');
			}
			
			s.append( opt );
		}
		 
		return s;
	}
});


it.load();




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
		var record = $(node).data('record');
		
		data['calendar_item[' + index + '][id]']          = record.calendar_item_id;
		data['calendar_item[' + index + '][start_date]']  = record.start_date;
		data['calendar_item[' + index + '][item_action]'] = itemAction;
	});

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=calendar&c=calitemActionOverview&a=update_item_action'),
		data: data,
		success: function(data, textStatus, xhr) {
			show_user_message('Changes saved');
			it.load();
		},
		error: function(xhr, textStatus, errorThrown) {
			alert('Error: ' + xhr.responseText);
		}
	});
}



</script>




