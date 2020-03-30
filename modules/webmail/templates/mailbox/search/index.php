

<div class="page-header">
	<h1>Mailarchive</h1>
</div>


<div id="mail-container">
	<div class="messages-content ui-layout-center">
		<div data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][0] : '' ?>">
			<div class="search-fields">
				<input type="text" name="q" placeholder="Search" style="width: 100%;" />
			</div>
			<div id="emailheader-table-container"></div>
			
		</div>
		<div id="mail-content" style="" data-dont-overflow="1" data-height-in-percentage="<?= isset($state['slider-ratio'][0]) ? $state['slider-ratio'][1] : '' ?>">
			<iframe style="width:100%; height: 150%;" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox"></iframe>
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

		$('[name=q]').focus();
	});
} else {
	$(document).ready(function() {
		$('#mail-container .messages-content').horizontalSplitContainer( opts );
		
		$('[name=q]').focus();
	});
}

</script>




<script>

var t = new IndexTable('#emailheader-table-container', {
	autoloadNext: true,
	fixedHeader: true,
	tableHeight: 'calc(100% - 35px)',
	searchContainer: '.search-fields'
});

t.setRowClick(function(row, evt) {

	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');

	var email_id = $(row).data('record').email_id;
	$('#mail-content iframe').attr('src', appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + email_id));
});

t.setRowDblclick(function(row, evt) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + $(row).data('record').email_id), '_blank');
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

		var btnSpam = $('<a class="fa fa-flag mark-as-spam" href="javascript:void(0);" />');
		btnSpam.click(function() {
			var c = confirm('Are you sure to mark this mail as spam?');
			if (c) {
				markMailAsSpam( $(this).closest('tr'), email_id );
			}
		});

		var btnHam = $('<a class="fa fa-thumbs-o-up mark-as-ham" href="javascript:void(0);" />');
		btnHam.click(function() {
			var c = confirm('Are you sure to mark this mail as HAM?');
			if (c) {
				markMailAsHam( $(this).closest('tr'), email_id );
			}
		});

		if (record.junk) {
			btnHam.show();
			btnSpam.hide();
		} else {
			btnHam.hide();
			btnSpam.show();
		}
		
		var container = $('<div />');
		container.append(btnSpam);
		container.append(btnHam);
		
		return container;
	}
});

t.load();



function markMailAsSpam(row, email_id) {
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mark_as_spam'),
		type: 'POST',
		data: {
			email_id: email_id
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				$(row).find('.td-mailbox-name').text('Junk');
				$(row).find('.mark-as-spam').hide();
				$(row).find('.mark-as-ham').show();
			}
		}
	});
}

function markMailAsham(row, email_id) {
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mark_as_ham'),
		type: 'POST',
		data: {
			email_id: email_id
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
// 				$(row).find('.td-mailbox-name').text('Junk');
				$(row).find('.mark-as-spam').show();
				$(row).find('.mark-as-ham').hide();
			}
		}
	});
}


</script>

