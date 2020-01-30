

<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=storefile&id='.$store->getStoreId()) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Upload new file, store: <?= esc_html($store->getStoreName()) ?></h1>
</div>


<?= $form->render() ?>
