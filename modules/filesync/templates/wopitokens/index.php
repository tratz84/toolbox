
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" id="btnPurge" class="fa fa-trash"></a>
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('WOPI tokens') ?></h1>
</div>




<div id="wopi-table-container"></div>




<script>


$('#btnPurge').click(function() {
	var c = confirm( toolbox_t('Are you sure to delete ALL tokens?') );
	if (!c) {
		return;
	}

	window.location = appUrl('/?m=filesync&c=wopitokens&a=delete_all');
});

var t = new IndexTable('#wopi-table-container');

t.setConnectorUrl( '/?m=filesync&c=wopitokens&a=search' );

t.addColumn({
	fieldName: 'access_token',
	fieldDescription: 'Token',
	fieldType: 'text',
	searchable: true,
	render: function(record) {
		var t = record.access_token;

		var s = $('<span />');
		s.text( t.substr(0, 40) + '....' );
		s.attr( 'title', t );
		
		return s;
	}
});
t.addColumn({
	fieldName: 'access_token_ttl_datetime',
	fieldDescription: 'TTL',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: 'path',
	fieldDescription: 'Path',
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'username',
	fieldDescription: 'Username',
	fieldType: 'text',
	searchable: true
});


t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Created',
	fieldType: 'datetime',
	searchable: false
});
t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', 'javascript:void(0);');
		anchDel.click(function() {
			var r = $(this).closest('tr').data('record');
			
			var c = confirm(toolbox_t('Are you sure to delete this token?') + ' ' + r.access_token);
			if (!c) {
				return;
			}

			$.ajax({
				type: 'POST',
				url: appUrl('/?m=filesync&c=wopitokens'),
				data: {
					a: 'delete',
					id: r.wopi_access_id
				},
				success: function(data, xhr, textStatus) {
					if (!data || !data.success) {
						alert( toolbox_t('Error deleting token') );
					}
					
					t.load();
				}
			});
		});

		
		var container = $('<div />');
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>
