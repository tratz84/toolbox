

<div class="widget-title">
	<div class="toolbox">
		<a href="javascript:void(0);" onclick="show_popup(appUrl('/?m=webmail&c=mailbox/dashboard&a=settings'));" class="fa fa-cog"></a>
	</div>

	<?= t('Recent e-mails') ?>
</div>

<table class="list-response-table">
	<thead>
		<tr>
			<th class="th-icons" style="min-width: 25px;"></th>
			<th class="th-folder-from-subject"></th>
			<th class="th-folder"><?= t('Box') ?></th>
			<th class="th-from"><?= t('From') ?></th>
			<th class="th-subject"><?= t('Subject') ?></th>
			<th class="th-action"><?= t('Action') ?></th>
			<th class="th-date"><?= t('Date') ?></th>
		</tr>
	</thead>
	<tbody class="mailbox-dashboard">
	</tbody>
</table>


<script>

function mailbox_showEmail(email_id) {
	if ($(window).width() <= 780) {
		window.open( appUrl('/?m=webmail&c=mailbox/mail&a=view&id=' + email_id), '_self');
	} else {
		window.open( appUrl('/?m=webmail&c=mailbox/search#email_id=' + email_id), '_self' );
	}
}

function renderMails(mails) {
	$('.mailbox-dashboard').empty();
	if (mails.length == 0) {
		$('.mailbox-dashboard').append('<tr><td colspan="6" class="no-results-found">' + toolbox_t('All e-mails handled') + '</td></tr>');
	} else {
		for(var i in mails) {
			var m = mails[i];

			var tdAnswered    = $('<td class="td-icons" />');
			if (m.answered) {
				tdAnswered.append('<span class="fa fa-reply" />');
			}
			
			var tdFolderFromSubject = $('<td class="td-folder-from-subject"><div class="date" /><div class="action" /><div class="folder" /><div class="from" /><div class="subject" /></td>');

			tdFolderFromSubject.find('.date').text( format_datetime(str2datetime(m.date), {skipSeconds: true}) );
			tdFolderFromSubject.find('.action').text( m.action );
			
			tdFolderFromSubject.find('.folder').text( m.mailbox_name + ' - ' );
			tdFolderFromSubject.find('.from').text( m.from_name );
			tdFolderFromSubject.find('.subject').text( m.subject );
			
			
			var tdMailboxName = $('<td class="td-folder" />');
			tdMailboxName.text( m.mailbox_name );
			
			var tdFromName    = $('<td class="td-from" />');
			tdFromName.text( m.from_name );

			
			var tdSubject     = $('<td class="td-subject" />');
			tdSubject.text( m.subject );
			
			var tdAction      = $('<td class="td-action" />');
			tdAction.text( m.action );
			
			var tdDate        = $('<td class="td-date" style="white-space: nowrap;" />');
			tdDate.text(format_datetime(str2datetime(m.date), {skipSeconds: true}));

			var tr = $('<tr />');
			tr.addClass('clickable');
			tr.addClass(m.seen ? 'seen' : 'unseen');
			tr.data('email-id', m.email_id);
			tr.click(function() {
				mailbox_showEmail($(this).data('email-id'));
			});
			
			
			tr.append(tdAnswered);
			tr.append(tdFolderFromSubject);
			tr.append(tdMailboxName);
			tr.append(tdFromName);
			tr.append(tdSubject);
			tr.append(tdAction);
			tr.append(tdDate);
			
			$('.mailbox-dashboard').append( tr );
		}
	}
}
var mailbox_mails = <?= json_encode($mails) ?>;
renderMails( mailbox_mails );

// refresh every minute
if (typeof webmail_dashboard_refreshInterval != 'undefined') clearInterval(webmail_dashboard_refreshInterval);
webmail_dashboard_refreshInterval = setInterval(function() {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=mailbox/dashboard&a=search'),
		success: function(data, xhr, textStatus) {
			console.log( data );
			if (data.success) {
				renderMails( data.mails );
			}
		}
	});
}, 30000);

</script>


