
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

	<h1>Mailarchive</h1>
</div>


<div id="mail-container" class="pretty-split-pane-frame stretch-to-bottom">
	<div class="split-pane horizontal-percent">
		<div class="split-pane-component" id="top-component">
			<div class="pretty-split-pane-component-inner">
    			<div class="search-fields">
    				<div class="toolbox" style="padding: 5px 5px 0 3px;">
    					<input type="checkbox" name="f" title="filters" <?= $filtersEnabled ? 'checked=checked':'' ?> />
    					<a href="javascript:void(0);" onclick="show_popup(<?= esc_json_attr(appUrl('/?m=webmail&c=mailbox/search&a=settings')) ?>);" class="fa fa-cog" style="font-size: 20px;"></a>
    				</div>
    				
    				<input type="text" name="q" placeholder="<?= t('Search') ?>" style="width: calc(100% - 50px);" />
    			</div>
    			<div id="emailheader-table-container" style="max-height: calc(100% - 35px);"></div>
			</div>
		</div>
		<div class="split-pane-divider" id="my-divider"></div>
		<div class="split-pane-component" id="bottom-component">
			<div id="mail-content" class="pretty-split-pane-component-inner">
			</div>
		</div>
	</div>
</div>



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
			url: appUrl('/?m=webmail&c=mailbox/search&a=savestate'),
			type: 'POST',
			data: {
				percentages: p
			}
		});
	});
}



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
		$('[name=q]').focus();

		execSplitPane();
	});
} else {
	$(document).ready(function() {
		$('[name=q]').focus();
		
		execSplitPane();
	});
}

</script>




<script>

var selectedMailId = null;

var t = new IndexTable('#emailheader-table-container', {
	autoloadNext: true,
	fixedHeader: true,
	searchContainer: '.search-fields, .list-response-table thead'
});

t.setRowClick(function(row, evt) {
	selectedMailId = $(row).data('record').email_id;
	
	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');
	$(row).removeClass('unseen');

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=mailbox/search&a=view'),
		data: {
			id: selectedMailId
		},
		success: function(data, xhr, textStatus) {
			$('#mail-content').html( data );

			$('#mail-content').find('[name=move_imap_folder]').change(function() {
				
			});
		}
	});
});

t.setRowDblclick(function(row, evt) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + $(row).data('record').email_id), '_blank');
});

t.setCallbackRenderRow(function(obj, row) {
	$(row).attr('email-id', obj.email_id);

	if (obj.seen == false) {
		$(row).addClass('unseen');
	}
});

t.setConnectorUrl( '/?m=webmail&c=mailbox/search&a=search' );

t.addColumn({
	fieldName: 'status',
	render: function(record) {
		var c = $('<div class="webmail-mail-status" />');
		
		if (record.answered) {
			c.append( '<span class="fa fa-reply"></span>' );
		}
		
		return c;
	}
});

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
	fieldName: 'action',
	fieldDescription: 'Action',
	fieldType: 'select',
	filterOptions: [
		{ value: '',          text: 'Action'    },
		{ value: 'open',      text: 'Open'      },
		{ value: 'urgent',    text: 'Urgent'    },
		{ value: 'replied',   text: 'Replied'   },
		{ value: 'ignored',   text: 'Ignored'   },
		{ value: 'done',      text: 'Done'      },
		{ value: 'postponed', text: 'Postponed' },
	],
	searchable: true,
	render: function(record) {
		return record.action;
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

$(window).on('webmail-reload', function() {
	window.location = appUrl('/?m=webmail&c=mailbox/search');
});

function moveMail(email_id, targetFolder) {
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=move_mail'),
		type: 'POST',
		data: {
			email_id: email_id,
			target_folder: targetFolder
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				$('tr[email-id="' + data.email_id + '"]').find('.td-mailbox-name').text( data.newFolder );
			}
		}
	});
	
}

function setMailAction(email_id, newAction) {
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mail_action'),
		type: 'POST',
		data: {
			email_id: email_id,
			action: newAction
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				$('tr[email-id="' + data.email_id + '"]').find('.td-action').text( data.action );
			}
		}
	});
	
}


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


function replyMail(email_id) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=reply&email_id=' + email_id), '_self');
}

function forwardMail(email_id) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=forward&email_id=' + email_id), '_self');
}



</script>

