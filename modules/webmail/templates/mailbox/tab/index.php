

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


<div id="mail-container" class="pretty-split-pane-frame stretch-to-bottom" style="height: 800px;">
	<div class="split-pane  horizontal-percent">
		<div class="split-pane-component" id="top-component">
			<div class="pretty-split-pane-component-inner">
				<div class="search-fields">
					<div class="toolbox">
						<a href="javascript:void(0);" onclick="mailboxTabSettings_Click();" style="font-size: 20px; margin: 7px 5px 0 0;" class="fa fa-cog"></a>
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
		</div>
		<div class="split-pane-divider context-background" id="my-divider"></div>
		<div class="split-pane-component" id="bottom-component">
			<div id="mail-content" class="pretty-split-pane-component-inner">
				<iframe style="width:100%; height: calc(100% - 10px);" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox"></iframe>
			</div>
		</div>
	</div>
</div>





<script>

var mailSplitPaneState = <?= json_encode(getJsState('customer-mailbox-tab-state')) ?>;


var it_webmail = new IndexTable('#emailheader-table-container', {
	autoloadNext: true,
	fixedHeader: true,
//	tableHeight: 'calc(100% - 35px)',
	searchContainer: '.search-fields'
});

it_webmail.setRowClick(function(row, evt) {
	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');

	var email_id = $(row).data('record').email_id;
	$('#mail-content iframe').attr('src', appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + email_id));

	$(this.container).find('tr.active').removeClass('active');
	$(this.container).find('tr[email-id="'+email_id+'"]').addClass('active');
});

it_webmail.setRowDblclick(function(row, evt) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + $(row).data('record').email_id), '_blank');
});

it_webmail.setConnectorUrl( '/?m=webmail&c=mailbox/search&a=search' );

it_webmail.setCallbackRenderRow( function(obj, row) {
	$(row).attr('email-id', obj.email_id);
});


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
var mailtab_first_open = true;
$(window).on('tabcontainer-item-click', function(e, f) {
	var tab_name = $( f ).data('tab-name');

	if (tab_name != 'mail')
		return;
	
	mail_tab_opened = true;

	if (mailtab_first_open) {
		// load mail
		it_webmail.load();

		$(window).on('webmail-reload', function() {
			it_webmail.load( { force: true } );
		});
		

		$('#mail-container .split-pane').splitPane();
		
		$('.split-pane').on('dividerdragend', function() {
			var p = [];
			var totalHeight = $('#mail-container').height();
			var tc = $('#mail-container #top-component').height();
			p.push( tc / totalHeight );
			p.push( 1-(tc / totalHeight) );

			// save state
			saveJsState('customer-mailbox-tab-state', p);
		});

		$(document).ready(function( ){
			$('#mail-container [name=q]').focus();
		});
		
		// set focus to search-field
		setTimeout(function() {
			$('#mail-container [name=q]').focus();

			// restore split pane state?
			if (Array.isArray(mailSplitPaneState)) {
    			var fcs = $('#mail-container').height() * mailSplitPaneState[0];
    			$('#mail-container .split-pane').splitPane('firstComponentSize', fcs);
			}
		}, 500);
	}
	
	mailtab_first_open = false;
});

</script>
