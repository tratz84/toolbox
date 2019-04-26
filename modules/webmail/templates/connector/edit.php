

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=connector') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Connector toevoegen</h1>
    <?php else : ?>
    <h1>Connector bewerken</h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>

<br/><br/>

<input type="button" class="clear" id="btnFetchImapFolders" value="Fetch imap-folders" />

<br/><br/>

<script>


$(document).ready(function() {
	if ($('[name=connector_id]').val() == '') {
		autosetPort();
	}
});

$('[name=connector_type]').change(function() {
	autosetPort();
});

function autosetPort() {
	if ($('[name=connector_type]').val() == 'imap') {
		$('[name=port]').val( 143 );
	}
	if ($('[name=connector_type]').val() == 'pop3') {
		$('[name=port]').val( 110 );
	}
}



$('#btnFetchImapFolders').click(function() {
	var data = serialize2object('.form-generator');
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=connector&a=fetch_folders'),
		data: data,
		success: function(data, xhr, textStatus) {

			var oldSelectedFolders = [];

			$('.widget-container-imap-folders input[type=checkbox]').each(function(index, node) {
				if ($(node).prop('checked')) {
					var t = $(node).closest('.widget').find('label').text();
					
					oldSelectedFolders.push( trim(t) );
				}
			});
			
			
			if (data.status == 'ok') {
				$('.widget-container-imap-folders').empty();

				var folderNo = 0;
				for (var i in data.folders) {
					var c = $('<div class="widget checkbox-field-widget " />');

					var inpHidden = $('<input type="hidden" name="imapfolders[]" />');
					inpHidden.val( data.folders[i] );
					c.append(inpHidden);
					
					var inp = $('<input type="checkbox" class="checkbox-ui" id="imap-folder-no'+folderNo+'" name="selectedImapfolders[]" />');
					inp.val( data.folders[i] );
					if (oldSelectedFolders.indexOf( data.folders[i] ) != -1) {
						inp.prop('checked', true);

						}
					c.append(' <label>' + data.folders[i] + '</label>');
					c.append(inp);
					c.append('<label for="imap-folder-no'+folderNo+'" class="checkbox-ui-placeholder"></label>');
					
					
					$('.widget-container-imap-folders').append( c );
					
					folderNo++;
				}

				
			} else {
				showAlert('Error', 'Error: ' + data.message);
			}
		}
	});
});

</script>


