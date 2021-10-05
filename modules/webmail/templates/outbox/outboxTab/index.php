



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

<div class="webmail-list-container">
<div id="outbox-mail-container" class="pretty-split-pane-frame stretch-to-bottom" style="height: 800px;">
	<div class="split-pane  horizontal-percent">
		<div class="split-pane-component" id="top-component">
			<div class="pretty-split-pane-component-inner">
				<div id="webmail-outbox-container"></div>
			</div>
		</div>
		<div class="split-pane-divider context-background" id="my-divider"></div>
		<div class="split-pane-component" id="bottom-component">
			<div id="outbox-mail-content" class="pretty-split-pane-component-inner">
			</div>
		</div>
	</div>
</div>
</div>






<script>


<?= $wo_it->render() ?>

//load IndexTable on tab activation
var outboxtab_first_open = true;
$(window).on('tabcontainer-item-click', function(e, f) {
	if (outboxtab_first_open == false) return;
	
	var tab_name = $( f ).data('tab-name');

	if (tab_name != 'mail-outbox')
		return;

	woutbox_it.setRowClick(function( row ) {
		$(row).closest('tbody').find('tr.active').removeClass( 'active' );
		$(row).addClass( 'active' );
		
		var rec = $(row).data('record');
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=webmail&c=email&a=view'),
			data: {
				id: rec.email_id
			},
			success: function(data, xhr, textStatus) {
				$('#outbox-mail-content').html( data );
			}
		});
	});

	woutbox_it.setRowDblclick(function(row) {
		var rec = $(row).data('record');
		
		window.open( appUrl('/?m=webmail&c=view&id=' + rec.email_id), '_blank' );
	});
	
	woutbox_it.load();

	$('#outbox-mail-container .split-pane').splitPane();

	setTimeout(function() {
    	var fcs = $('#outbox-mail-container').height() * 0.4;
    	$('#outbox-mail-container .split-pane').splitPane('firstComponentSize', fcs);
	}, 500);

	
	
	outboxtab_first_open = false;
});

</script>
