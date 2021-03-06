



function markMailAsSpam( email_id ) {
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mark_as_spam'),
		type: 'POST',
		data: {
			email_id: email_id
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
            	var row = $('#emailheader-table-container tr[email-id="' + email_id + '"]');

            	var newFolder = data.folder ? data.folder : 'Junk';
            	
				$(row).find('.td-mailbox-name').text( newFolder );
				$(row).find('.mark-as-spam').hide();
				$(row).find('.mark-as-ham').show();

				// update folder select-box
				var actionBox = $('.action-box.mail-actions');
				if (actionBox.data('email-id') == email_id) {
    				$(actionBox).find('[name=move_imap_folder]').val( newFolder );
				}
			}
		}
	});
}

function markMailAsHam(row, email_id) {
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mark_as_ham'),
		type: 'POST',
		data: {
			email_id: email_id
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
// 				$(row).find('.td-mailbox-name').text('Junk');
				$(row).find('.mark-as-spam').show();
				$(row).find('.mark-as-ham').hide();
			}
		}
	});
}

function replyMail(email_id) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=reply&email_id=' + email_id), '_self');
}

function forwardMail(email_id) {
	window.open(appUrl('/?m=webmail&c=mailbox/mail&a=forward&email_id=' + email_id), '_self');
}

function moveMail(email_id, targetFolder) {
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=move_mail'),
		type: 'POST',
		data: {
			email_id: email_id,
			target_folder: targetFolder
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				$('tr[email-id="' + data.email_id + '"]').find('.td-mailbox-name').text( data.newFolder );
			}
		}
	});
	
}

function setMailAction(email_id, newAction) {
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=mail_action'),
		type: 'POST',
		data: {
			email_id: email_id,
			action: newAction
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				$('tr[email-id="' + data.email_id + '"]').find('.td-action').text( data.action );
			}
		}
	});
	
}

function deleteMail(email_id) {
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/mail&a=delete_mail'),
		type: 'POST',
		data: {
			email_id: email_id
		},
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
			} else {
				// remove record & content from page
				$('tr[email-id="'+email_id+'"]').remove();
				$('#mail-content').empty();
				
				// ajax-param set? => unset
				var l = window.location.toString();
				if (l.indexOf('#') != -1) {
					l = l.substr(0, l.indexOf('#'));
					history.replaceState( {}, '', l );
				}
			}
		}
	});
}

