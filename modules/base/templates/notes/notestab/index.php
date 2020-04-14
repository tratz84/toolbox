
<div class="action-box" style="padding: 15px 0 0 5px;">
	
	<button onclick="newNote_Click();"><?= t('New note') ?></button>
	
</div>


<hr/>

<table class="list-response-table notes-table">
	<thead>
		<tr>
			<th><?= t('Note') ?></th>
			<th style="width: 150px;"><?= t('Date') ?></th>
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
			personId: <?= json_encode($personId) ?>
		},
		success: function(data, xhr, textStatus) {
			renderNotes( data.listResponse );
		}
	});
}

function renderNotes(listResponse) {
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
		
		var tdDate      = $('<td />');
		tdDate.text( format_datetime(str2datetime(o.edited)) );

		var tdImportant = $('<td />');
		tdImportant.text( o.important ? _('Yes') : _('No') );
		
		var tdActions   = $('<td class="actions" />');
		tdActions.append('<a href="javascript:void(0);" class="fa fa-trash delete"></a>');
		tdActions.find('.delete').click(function() {
			deleteNote_Click( $(this).closest('tr').data('note-id') );
		});

		tr.append( tdNote );
		tr.append( tdDate );
		tr.append( tdImportant );
		tr.append( tdActions);
		
		$('.notes-table tbody').append( tr );
	}

	if (listResponse.rowCount == 0) {
		$('.notes-table tbody').append('<tr><td colspan="4" style="font-style: italic; text-align: center;">'+_('No notes')+'</td></tr>');
	}
}


function newNote_Click() {
	show_popup(appUrl('/?m=base&c=notes/notestab&a=edit_note'), {
		data: {
			company_id: <?= json_encode( $companyId ) ?>,
			person_id:  <?= json_encode( $personId ) ?>,
			ref_object: <?= json_encode($ref_object) ?>,
			ref_id: <?= json_encode($ref_id) ?>
		},
		renderCallback: function(popup) {
			$(popup).find('[type=text]').first().focus();
		}
	});
}

function editNote( note_id ) {
	show_popup(appUrl('/?m=base&c=notes/notestab&a=edit_note'), {
		data: {
			note_id: note_id
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




