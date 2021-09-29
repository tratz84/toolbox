
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=fail2ban&c=ipsettings&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('Black/white lists') ?></h1>
</div>



<?= $indexTable->renderHtml() ?>




<script>
it_fis.setCallbackRenderRows(function() {
	var l = $('#is-container .list-response-table tbody');
	console.log( l.length );

	console.log( $('#is-container .list-response-table tbody tr').length );
	
	$('#is-container .list-response-table tbody').sortable({
		handle: '.fa-sort',
		update: function(evt) {
			var ids = new Array();

			$('#is-container .list-response-table tbody tr').each(function(index, node) {
				ids.push( $(node).data('record').ip_setting_id );
			});

			console.log( ids );

			$.ajax({
				type: 'POST',
				url: appUrl('/?m=fail2ban&c=ipsettings&a=save_sort'),
				data: {
					ids: ids.join(',')
				}
			});

		}
	});

	it_fis.setCallbackRenderDone( null );
});

</script>


