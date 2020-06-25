
<div class="action-box" style="padding: 15px 0 0 5px;">
	
	<button onclick="newNote_Click();"><?= t('New note') ?></button>
	
</div>


<hr/>

<table class="list-response-table notes-table">
	<thead>
		<tr>
			<th><?= t('Note') ?></th>
			<th style="width: 150px;"><?= t('Edited') ?></th>
			<th style="width: 150px;"><?= t('Created') ?></th>
			<th style="width: 50px;"><?= t('Important') ?></th>
			<th style="width: 200px;"></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>




<script>

function reloadNotes() {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=notes/notestab&a=search'),
		data: {
			companyId: <?= json_encode($companyId) ?>,
			personId: <?= json_encode($personId) ?>,
			ref_object: <?= json_encode($ref_object) ?>,
			ref_id: <?= json_encode($ref_id) ?>,
			save_by_ref: <?= json_encode($save_by_ref?1:0) ?>
		},
		success: function(data, xhr, textStatus) {
			renderNotes( data.listResponse );
		}
	});
}

function renderNotes(listResponse) {

	var importantMessage = '';
	
	$('.notes-table tbody').empty();
	for(var x=0; x < listResponse.rowCount; x++) {
		var o = listResponse.objects[x];
		
		var tr = $('<tr class="clickable" />');
		tr.click(function(evt) {
			if ($(evt.target).hasClass('actions') || $(evt.target).closest('td.actions').length > 0)
				return;

			var noteId = $(this).data('note-id');

			editNote( noteId );
		});
		
		tr.attr('data-note-id', o.note_id);
		tr.data('note-id', o.note_id);
		var tdNote      = $('<td class="td-summary" />');
		tdNote.text( o.summary );
		
		var tdEdited = $('<td />');
		tdEdited.text( format_datetime(str2datetime(o.edited)) );

		var tdCreated = $('<td />');
		tdCreated.text( format_datetime(str2datetime(o.created)) );
		
		var tdImportant = $('<td />');
		tdImportant.text( o.important ? _('Yes') : _('No') );
		
		var tdActions   = $('<td class="actions" />');
		tdActions.append('<a href="javascript:void(0);" class="fa fa-trash delete"></a>');
		tdActions.find('.delete').click(function() {
			deleteNote_Click( $(this).closest('tr').data('note-id') );
		});

		tr.append( tdNote );
		tr.append( tdEdited );
		tr.append( tdCreated );
		tr.append( tdImportant );
		tr.append( tdActions);
		
		$('.notes-table tbody').append( tr );


		if (o.important) {
			if (importantMessage != '')
				importantMessage += '\n';
			importantMessage = importantMessage + o.summary;
		}
	}

	if (listResponse.rowCount == 0) {
		$('.notes-table tbody').append('<tr><td colspan="4" style="font-style: italic; text-align: center;">'+_('No notes')+'</td></tr>');
	}
	

	var importantMessageShown = $('.notes-table').data('important-message-shown') ? true : false;
	if (importantMessageShown == false) {
		if (importantMessage != '') {
			show_user_warning(_('Please note')+': ' + importantMessage, { timeout: 5000 });
		}

		$('.notes-table').data('important-message-shown', true);
	}
}


function newNote_Click() {
	show_popup(appUrl('/?m=base&c=notes/notestab&a=edit_note'), {
		data: {
			company_id: <?= json_encode( $companyId ) ?>,
			person_id:  <?= json_encode( $personId ) ?>,
			ref_object: <?= json_encode($ref_object) ?>,
			ref_id: <?= json_encode($ref_id) ?>,
			save_by_ref: <?= json_encode($save_by_ref?1:0) ?>
		},
		renderCallback: function(popup) {
			$(popup).find('[type=text]').first().focus();
		}
	});
}

function editNote( note_id ) {
	show_popup(appUrl('/?m=base&c=notes/notestab&a=edit_note'), {
		data: {
			note_id: note_id,
			ref_object: <?= json_encode($ref_object) ?>,
			ref_id: <?= json_encode($ref_id) ?>,
			save_by_ref: <?= json_encode($save_by_ref?1:0) ?>
		},
		renderCallback: function(popup) {
			$(popup).find('[type=text]').first().focus();
		}
	});
}

function deleteNote_Click( note_id ) {
	var c = confirm('Are you sure to delete note "' + $('tr[data-note-id=' + note_id + '] td.td-summary').text() + '" ?');
	if (!c)
		return;


	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=notes/notestab&a=delete'),
		data: {
			note_id: note_id
		},
		complete: function() {
			reloadNotes();
		}
	});
	
}

$(document).ready(function() {
	var lr = <?= json_encode($listResponse) ?>;

	renderNotes( lr );
});

</script>




