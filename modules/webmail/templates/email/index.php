

<div class="page-header">
	<h1>Outbox</h1>
</div>


<div id="mail-container">
	<div class="messages-content ui-layout-center">
		<div data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][0] : '' ?>">
			
			<div id="emailheader-table-container"></div>
			
		</div>
		<div id="mail-content" style="" data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][1] : '' ?>">
		</div>
	</div>
</div>


<script>

function uploadFilesField_Click(obj) {
	window.open(appUrl('/?m=webmail&c=view&a=file&id=' + $(obj).data('id')), '_blank');
}

</script>

<script>

var opts = {
	onresize: function(containerSlider) {
		var p = containerSlider.getPanelPercentages();
		$.ajax({
			url: appUrl('/?m=webmail&c=email&a=savestate'),
			type: 'POST',
			data: {
				percentages: p
			}
		});
	}
};

if (typeof less != 'undefined') {
	less.pageLoadFinished.then(function() {
		$('#mail-container .messages-content').horizontalSplitContainer( opts );
	});
} else {
	$(document).ready(function() {
		$('#mail-container .messages-content').horizontalSplitContainer( opts );
	});
}

</script>




<script>

var t = new IndexTable('#emailheader-table-container', {
	autoloadNext: true,
	fixedHeader: true
});

t.setRowClick(function(row, evt) {

	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=email&a=view'),
		data: {
			id: $(row).data('record').email_id
		},
		success: function(data) {
			$('#mail-content').html( data );
		}
	});
});

t.setRowDblclick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=view&id=' + $(row).data('record').email_id);
});

t.setConnectorUrl( '/?m=webmail&c=email&a=search' );


t.addColumn({
	fieldName: 'email_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'from_name',
	fieldDescription: 'Van',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'customer_name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(row) {
		if (row.company_name) {
			return row.company_name;
		} else {
			var t = '';

			if (row.lastname)
				t += row.lastname;
			if (row.insert_lastname) {
				t += ', ' + row.insert_lastname;
			}
			if (row.firstname) {
				t += ' ' + row.firstname;
			}
			return t;
		}
	},
	searchable: false
});

t.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Onderwerp',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'status',
	fieldDescription: 'Status',
	fieldType: 'text',
	render: function(record) {
		if (record.status == 'draft') {
			return 'Concept';
		} else if (record.status == 'sent') {
			return 'Verzonden';
		} else {
			return record.status;
		}
	}
});

t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var email_id = record['email_id'];
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=webmail&c=email&a=delete&id=' + email_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.subject);

		
		var container = $('<div />');
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>