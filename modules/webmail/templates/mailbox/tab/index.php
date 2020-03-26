


<div id="mail-container" class="tab-mailbox-container" style="height: 100%;">
	<div style="height: 200px; overflow-y: scroll;">
		<div class="search-fields">
			<div class="toolbox">
				<a href="javascript:void(0);" onclick="mailboxTabSettings_Click();" class="fa fa-cog"></a>
			</div>
			<input type="hidden" name="mailtab" value="1" />
			<?php if (isset($companyId)) : ?>
			<input type="hidden" name="company_id" value="<?= esc_attr($companyId) ?>" />
			<?php endif; ?>
			<?php if (isset($personId)) : ?>
			<input type="hidden" name="person_id" value="<?= esc_attr($personId) ?>" />
			<?php endif; ?>
			<input type="text" name="q" placeholder="Search" style="width: calc(100% - 50px);" />
		</div>
		<div id="emailheader-table-container"></div>
	</div>
	<div id="mail-content" style="height: 500px; min-height: 500px;">
		<iframe style="width:100%; height: 150%;" frameborder="0" sandbox="allow-popups"></iframe>
	</div>
</div>



<script>


var it_webmail = new IndexTable('#emailheader-table-container', {
	autoloadNext: true,
	fixedHeader: true,
	tableHeight: 'calc(100% - 35px)',
	searchContainer: '.search-fields'
});

it_webmail.setRowClick(function(row, evt) {

	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');

	var email_id = $(row).data('record').email_id;
	$('#mail-content iframe').attr('src', appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + email_id));
});

it_webmail.setRowDblclick(function(row, evt) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + $(row).data('record').email_id), '_blank');
});

it_webmail.setConnectorUrl( '/?m=webmail&c=mailbox/search&a=search' );



it_webmail.addColumn({
	fieldName: 'mailbox_name',
	fieldDescription: 'Mailbox',
	fieldType: 'text',
	searchable: false
});

it_webmail.addColumn({
	fieldName: 'from_name',
	fieldDescription: 'Van',
	fieldType: 'text',
	searchable: false
});

it_webmail.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Onderwerp',
	fieldType: 'text',
	searchable: false
});
it_webmail.addColumn({
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

it_webmail.addColumn({
	fieldName: 'date',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

it_webmail.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var email_id = record['email_id'];
		
		var container = $('<div />');
		
		return container;
	}
});

function mailboxTabSettings_Click() {

	show_popup(appUrl('/?m=webmail&c=mailbox/tab&a=settings'), {
		data: {
			companyId: <?= json_encode(isset($companyId) ? $companyId : null) ?>,
    		personId:  <?= json_encode(isset($personId)  ? $personId  : null) ?>
		}
	});
	
}


// load IndexTable on tab activation
$(window).on('tabcontainer-item-click', function(e, f) {
	var tab_name = $( f ).data('tab-name');

	if (tab_name != 'mail')
		return;

	// TODO: load mail
	it_webmail.load();
});

</script>
