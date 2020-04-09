
<link rel="stylesheet" href="<?= BASE_HREF ?>lib/split-view-pane/split-pane.css" />
<link rel="stylesheet" href="<?= BASE_HREF ?>lib/split-view-pane/pretty-split-pane.css" />
<script src="<?= BASE_HREF ?>lib/split-view-pane/split-pane.js"></script>

<style type="text/css">
.pretty-split-pane-frame { padding: 0; }
.pretty-split-pane-component-inner { padding: 0; }
#mail-content { padding: 0 6px; }
#top-component {
	margin-bottom: 5px;
	min-height: 50px;
}

#my-divider {
	height: 5px;
	background-color: #f00;
}

#bottom-component {
	min-height: 50px;
}

</style>


<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=view') ?>" class="fa fa-plus"></a>
	</div>
	<h1>Outbox</h1>
</div>


<div id="mail-container" class="pretty-split-pane-frame stretch-to-bottom">
	<div class="split-pane  horizontal-percent">
		<div class="split-pane-component" id="top-component">
			<div id="emailheader-table-container" class="pretty-split-pane-component-inner"></div>
		</div>
		<div class="split-pane-divider context-background" id="my-divider"></div>
		<div class="split-pane-component" id="bottom-component">
			<div id="mail-content" class="pretty-split-pane-component-inner"></div>
		</div>
	</div>
</div>


<script>

function uploadFilesField_Click(obj) {
	window.open(appUrl('/?m=webmail&c=view&a=file&id=' + $(obj).data('id')), '_blank');
}

</script>

<script>

var paneState = <?= json_encode($state) ?>;


function execSplitPane() {
	
	$('.split-pane').splitPane();
	
	if (paneState['slider-ratio'][0]) {
		var mch = $('#mail-container').height();
		
		var s = parseInt( mch * paneState['slider-ratio'][0] );
		$('.split-pane').splitPane('firstComponentSize', s);
	}
	
	$('.split-pane').on('dividerdragend', function() {
		var p = [];
		var totalHeight = $('#mail-container').height();
		var tc = $('#mail-container #top-component').height();
		p.push( tc / totalHeight );
		p.push( 1-(tc / totalHeight) );
		
		$.ajax({
			url: appUrl('/?m=webmail&c=email&a=savestate'),
			type: 'POST',
			data: {
				percentages: p
			}
		});
	});
}

if (typeof less != 'undefined') {
	less.pageLoadFinished.then(function() {
		execSplitPane();
	});
} else {
	$(document).ready(function() {
		execSplitPane();
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


// t.addColumn({
// 	fieldName: 'email_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
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