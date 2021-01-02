
<link rel="stylesheet" href="<?= BASE_HREF ?>lib/split-view-pane/split-pane.css" />
<link rel="stylesheet" href="<?= BASE_HREF ?>lib/split-view-pane/pretty-split-pane.css" />
<script src="<?= BASE_HREF ?>lib/split-view-pane/split-pane.js"></script>

<style type="text/css">
.pretty-split-pane-frame { padding: 0; }
.pretty-split-pane-component-inner { padding: 0; }
#mail-content { padding: 0 6px; height: calc(100% - 5px); }
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

#left-component { width: 200px; min-width: 50px; }
#vertical-divider { width: 5px; background-color: #f00; }

.filter-container {
    background-color: #fff;
}


</style>

<div class="page-header hidden">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=view') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Mailarchive</h1>
</div>


<div id="mail-container" class="pretty-split-pane-frame stretch-to-bottom">

	<div class="split-pane fixed-left">
		<div class="split-pane-component filter-container" id="left-component">
			<div class="action-box" style="margin: 20px 0 20px;">
				<span><a href="<?= appUrl('/?m=webmail&c=view') ?>"> <?= t('New1') ?></a></span>
			</div>
			<hr style="margin-bottom: 9px;" />
			<div>
    			<a href="javascript:void(0);" onclick="show_popup(<?= esc_json_attr(appUrl('/?m=webmail&c=mailbox/search&a=settings')) ?>);" class="fa fa-cog" style="font-size: 20px;"></a>
    			<label>
    				<input type="checkbox" name="f" title="filters" <?= $filtersEnabled ? 'checked=checked':'' ?> />
    				<?= t('Apply default filters') ?>
    			</label>
    			
    			<div class="facet-filters"></div>
			</div>
		</div>
		<div class="split-pane-divider context-background" id="vertical-divider"></div>
		
		<div class="split-pane-component mail-header-content-component" id="right-component">
		
        	<div class="split-pane horizontal-percent">
        		<div class="split-pane-component mail-headers" id="top-component">
        			<div class="search-fields">
        				<input type="text" name="q" placeholder="<?= t('Search') ?>" style="width: calc(100% - 7px); margin-left: 5px;" />
        			</div>
        			<div id="emailheader-table-container" style="max-height: calc(100% - 35px);"></div>
        		</div>
        		<div class="split-pane-divider context-background" id="my-divider"></div>
        		<div class="split-pane-component pretty-split-pane-component-inner" id="bottom-component">
        			<div id="mail-content"></div>
        		</div>
        	</div>
		
		</div>
	
	</div>

</div>



<script>

var paneState = <?= json_encode($state) ?>;


function execSplitPane() {
	$('.split-pane').splitPane();

	// set filter width
	if (paneState['filterWidth']) {
		var mcw = $('#mail-container').width();
		var s = parseInt( mcw * paneState['filterWidth'] );
		$('#mail-container .split-pane').splitPane('firstComponentSize', s);
	}
	// set mail header height
	if (paneState['mailHeaders']) {
		var mch = $('#mail-container').height();
		var s = parseInt( mch * paneState['mailHeaders'] );
		$('.mail-header-content-component .split-pane').splitPane('firstComponentSize', s);
	}
	
	
	$('.split-pane').on('dividerdragend', function() {
		var p = {};

		var totalHeight = $('#mail-container').height();
		var totalWidth = $('#mail-container').width();
		
		// calculate percentages for filter-width + mail-container
		var fw = $('.filter-container').width();
		p.filterWidth = fw / totalWidth;
		
		
		// calculate percentages for header + mail cnotent
		var tc = $('#mail-container .mail-headers').height();
		p.mailHeaders = tc / totalHeight;
		p.mailContent = 1-(tc / totalHeight);
		
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
	searchContainer: '.search-fields, .list-response-table thead, .filter-container'
});

t.setRowClick(function(row, evt) {
	selectedMailId = $(row).data('record').email_id;

	viewMail( selectedMailId );
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

t.setCallbackRenderRows(function() {
	// highlight row if email_id is set in url
	if (email_id = getAjxParam('email_id')) {
		if (!this.emailFormUrlSet) {
			this.emailFormUrlSet = true;
			viewMail( email_id );
		}
	}
});

t.setCallbackRenderDone(function() {
	var prevState = serialize2object('.facet-filters');
	
	$('.facet-filters').empty();

	console.log(this);

	if (!this.lastResponse || !this.lastResponse.filters) {
		return;
	}
	
	var filters = this.lastResponse.filters;

	var cff = $('<div class="facet-filter-item-container facet-folders" />');
	cff.append('<div class="facet-header">'+_('Folders')+'</div>');
	cff.append('<div><label><input type="radio" id="folder-show-all" name="folder" onchange="t.load({reset: true});" value="" /> '+_('Show all')+'</label></div');
	if (!prevState.folder) {
		cff.find('#folder-show-all').prop('checked', true);
	}
	
	// folder-filters
	if (filters.folders) {
		for(var i in filters.folders) {
			// create radio-button
			var inp = $('<input type="radio" name="folder" />');
			inp.val( filters.folders[i].name );
			inp.change(function() {
				t.load({reset: true});
			});

			if (prevState.folder == filters.folders[i].name)
				inp.prop('checked', true);

			// add label
			var lbl = $('<label />');
			lbl.append(inp);
			lbl.append( ' ' + filters.folders[i].name );

			// put it in a container
			var c = $('<div />');
			c.append( lbl );
			cff.append( c );
		}
	}
	$('.facet-filters').append(cff);
	
});

t.setConnectorUrl( '/?m=webmail&c=mailbox/search&a=search' );

t.addColumn({
	fieldName: 'status',
	render: function(record) {
		var c = $('<div class="webmail-mail-status" />');
		
		if (record.answered) {
			c.append( '<span class="fa fa-reply"></span>' );
		}
// 		if (record.forwarded) {
// 			c.append( '<span class="fa fa-share"></span>' );
// 		}
		
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
	fieldName: 'props',
	fieldDescription: '',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		var c = $('<div />');
		if (row.has_file_attachments) {
			c.append('<span class="fa fa-paperclip" />');
		}
		return c;
	}
});

var mapMailActions = <?= json_encode(mapMailActions()) ?>;
var actionFilterOptions = [];
actionFilterOptions.push( { value: '', text: 'Action' } );
for (var key in mapMailActions) {
	actionFilterOptions.push( { value: key, text: mapMailActions[key] } );
}

t.addColumn({
	fieldName: 'action',
	fieldDescription: 'Action',
	fieldType: 'select',
	filterOptions: actionFilterOptions,
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
				markMailAsSpam( email_id );
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

function viewMail(email_id) {
	var row = $('#emailheader-table-container tr[email-id="' + email_id + '"]');

	$('#emailheader-table-container tr.active').removeClass('active');
	$(row).addClass('active');
	$(row).removeClass('unseen');

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=mailbox/search&a=view'),
		data: {
			id: email_id
		},
		success: function(data, xhr, textStatus) {
			$('#mail-content').html( data );
			$('#mail-content').trigger('resize');
		}
	});
}



</script>

