
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=template/fileEditor&n'.urlencode($templateName)) ?>" class="fa fa-circle-chevron-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<h1>Template settings</h1>
</div>


<?= $form->render() ?>

