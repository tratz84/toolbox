

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=archive&a=upload&store_id='.$store->getStoreId()) ?>" class="fa fa-upload"></a>
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Files in') ?> <?= esc_html($store->getStoreName()) ?></h1>
</div>


<div id="storefile-table-container" class="autofocus-first-field"></div>


<script>

<?= $archiveCustomerIndexTable->render() ?>



</script>

