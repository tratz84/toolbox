
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Overview last failed checks') ?></h1>
</div>


<?= $ac->render() ?>

<?= $it->renderHtml() ?>




<script>


function cleanup_Click() {
	showConfirmation(t('Clean up failed tasks'), 'Filter <input type="text" id="failed-task-filter" value="*" />', function() {
		var f = $('#failed-task-filter').val();

		formpost( appUrl('/?m=fail2ban&c=check&a=delete'), {
			f: f
		});
	});
}


</script>


