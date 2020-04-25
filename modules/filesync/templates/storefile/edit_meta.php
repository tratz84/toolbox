
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=storefile&id='.$form->getWidgetValue('store_id')) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$form->getWidgetValue('store_file_id')) ?>" target="_blank" class="fa fa-download"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<h1>Metadata bewerken</h1>
</div>

<?= $actionContainer->render() ?>


<?= $form->render() ?>

