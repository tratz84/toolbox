

<div class="page-header">
	<h1>Mailarchive</h1>
</div>


<div id="mail-container">
	<div class="messages-content ui-layout-center">
		<div data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][0] : '' ?>">
			
			<div id="emailheader-table-container"></div>
			
		</div>
		<div id="mail-content" style="" data-dont-overflow="1" data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][1] : '' ?>">
			<iframe style="width:100%; height: 150%;" frameborder="0" sandbox=""></iframe>
		</div>
	</div>
</div>




<script>

var opts = {
	onresize: function(containerSlider) {
		var p = containerSlider.getPanelPercentages();
		$.ajax({
			url: appUrl('/?m=webmail&c=mailbox/search&a=savestate'),
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

	var email_id = $(row).data('record').email_id;
	$('#mail-content iframe').attr('src', appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + email_id));
});

t.setRowDblclick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=view&id=' + $(row).data('record').email_id);
});

t.setConnectorUrl( '/?m=webmail&c=mailbox/search&a=search' );



t.addColumn({
	fieldName: 'mailbox_name',
	fieldDescription: 'Mailbox',
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
	fieldName: 'date',
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
		
		var container = $('<div />');
		
		return container;
	}
});

t.load();

</script>