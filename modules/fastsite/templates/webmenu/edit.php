
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webmenu') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
		<a href="<?= appUrl('/?m=fastsite&c=webmenu&a=delete&id='.$form->getWidgetValue('webmenu_id')) ?>" class="fa fa-trash"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1>Menu item aanmaken</h1>
	<?php else : ?>
	<h1>Menu item bewerken</h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>


<script>

$(document).ready(function() {
	renderRoutingInput();
	$('[name=webpage_id]').change(function() { renderRoutingInput(); });
});



function renderRoutingInput() {
	var webpage_id = $('[name=webpage_id]').val();

	if (webpage_id  == '' || webpage_id == undefined) {
		$('.url-widget').show();
	} else {
		$('.url-widget').hide();
	}
}




</script>

