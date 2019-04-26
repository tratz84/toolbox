
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=invoice&c=offer&a=edit&id='.$offer->getOfferId()) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="document.getElementById('send').value=1; $(this).prop('disabled', true);" class="fa fa-send submit-form"></a>
	</div>

	<h1>SignRequest versturen</h1>
</div>

<div class="container-mailing-form">
	
	<?php print $form->render() ?>
	
</div>


<script>

$(document).ready(function() {
	$('form.form-generator').append('<input type="hidden" id="send" name="send" value="0" />');
});

</script>
