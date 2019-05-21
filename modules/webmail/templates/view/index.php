
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=email') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=email&a=delete&id='.$form->getWidgetValue('email_id')) ?>" class="fa fa-trash delete-email"></a>
		<?php if (hasCapability('webmail', 'send-mail')) : ?>
			<a href="javascript:void(0);" onclick="sendEmail_Click();" class="fa fa-send"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>E-mail</h1>
</div>

<div class="container-mailing-form">
	
	<?php print $form->render() ?>
	
</div>


<script>

var mailStatus = <?php print json_encode($emailStatus) ?>;

$(document).ready(function() {
	$('.delete-email').click( handle_deleteConfirmation_event );
});

function uploadFilesField_Click(obj) {
	window.open(appUrl('/?m=webmail&c=view&a=file&id=' + $(obj).data('id')), '_blank');
}




function sendEmail_Click() {
	var t = '';
	if (mailStatus == 'draft') {
		t = 'Weet u zeker dat u de e-mail wilt versturen?';
	} else {
		t = 'Weet u zeker dat u de e-mail <b>nogmaals</b> wilt versturen?';
	}

	
	showConfirmation('E-mail versturen', t, function() {
		var frm = $('.form-email-form');
		var data = serialize2object( frm );
		
		frm.append('<input type="hidden" name="sendmail" value="1" />');
		frm.submit();
	});
}


</script>

