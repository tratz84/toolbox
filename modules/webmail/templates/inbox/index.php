

<div class="page-header">
	<h1>Inbox</h1>
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
	autoloadNext: true
});

t.setRowClick(function(row, evt) {

	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=inbox&a=view'),
		data: {
			id: $(row).data('record').id
		},
		success: function(data) {
			$('#mail-content').html( data );
		}
	});
});

t.setConnectorUrl( '/?m=webmail&c=inbox&a=search' );


t.addColumn({
	fieldName: 'fromName',
	fieldDescription: 'Van',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Onderwerp',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'date',
	fieldDescription: 'Datum',
	fieldType: 'datetime',
	searchable: false
});

t.load();

</script>