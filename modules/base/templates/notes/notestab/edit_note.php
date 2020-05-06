
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" class="fa fa-save save-note submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1><?= t('New note') ?></h1>
	<?php else : ?>
	<h1><?= t('Edit note') ?></h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>


<script>

$('.popup-container .save-note').click(function() {
	saveNote();
});
function saveNote() {

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=notes/notestab&a=save_note'),
		data: $('.form-note-form').serialize(),
		success: function(data, xhr, textStatus) {
			if (data.success) {
				reloadNotes();

				close_popup();
				
				show_user_message(_('Note saved'));
			}
			else {
				$('.popup-container .errors.error-list').remove();
				
				var errorContainer = $('<div class="errors error-list"><ul></ul></div>');
				$('.popup-container .form-note-form').prev().append( errorContainer );
				
				for(var fieldName in data.errors) {
					var errs = data.errors[fieldName];
					$('.popup-container').find('[name=' + fieldName + ']').closest('div.widget').addClass('error');

					for(var i in errs) {
						errorContainer.find('ul').append('<li>' + fieldName + ' - ' + errs[i] + '</li>');
					}
				}
			}
		},
		error: function(xhr) {
			alert('Error saving note');
		}
	});
}


</script>
