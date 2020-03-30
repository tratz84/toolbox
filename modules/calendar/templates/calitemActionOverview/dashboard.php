

<div class="widget-title">
	Kalender: Actie punten
</div>

<div id="calendar-item-actions-table-container"></div>


<script>


var map_itemActions = <?= json_encode($map_itemActions) ?>;


var it_itemActions = new IndexTable('#calendar-item-actions-table-container');

it_itemActions.setConnectorUrl( '/?m=calendar&c=calitemActionOverview&a=search&calendar_id=<?= $calendar_id ?>' );

it_itemActions.setCallbackRenderRows(function() {
	$('#calendar-item-actions-table-container tbody tr').each(function(index, node) {
		$(node).find('td.td-actions select').change(function() {
			var tr = $(this).closest('tr');

			update_itemAction( tr, $(this).val() );
		});
	});
});

it_itemActions.addColumn({
	fieldName: 'start_date',
	width: 125,
	fieldDescription: 'Date / time',
	fieldType: 'date',
	searchable: false,
	render: function(record) {
		var t = format_date( str2date(record.start_date), { dmy: true });
		if (record.start_time) {
			t = t + ' ' + record.start_time;
		}

		return t;
	}
});
it_itemActions.addColumn({
	fieldName: 'description',
	fieldDescription: 'Description',
	fieldType: 'text',
	searchable: false
});
it_itemActions.addColumn({
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


it_itemActions.load();


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
// 			show_user_message('Changes saved');
			it_itemActions.load();
		},
		error: function(xhr, textStatus, errorThrown) {
			alert('Error: ' + xhr.responseText);
		}
	});
}



</script>


