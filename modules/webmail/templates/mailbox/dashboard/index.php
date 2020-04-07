

<div class="widget-title">
	<div class="toolbox">
		<a href="javascript:void(0);" onclick="show_popup(appUrl('/?m=webmail&c=mailbox/dashboard&a=settings'));" class="fa fa-cog"></a>
	</div>

	<?= t('Recent e-mails') ?>
</div>

<table class="list-response-table">
	<thead>
		<tr>
			<th style="min-width: 25px;"></th>
			<th><?= t('Box') ?></th>
			<th><?= t('From') ?></th>
			<th><?= t('Subject') ?></th>
			<th><?= t('Action') ?></th>
			<th><?= t('Date') ?></th>
		</tr>
	</thead>
	<tbody class="mailbox-dashboard">
	</tbody>
</table>


<script>

function mailbox_showEmail(email_id) {
	window.open( appUrl('/?m=webmail&c=mailbox/search#email_id=' + email_id), '_self' );
}

function renderMails(mails) {
	$('.mailbox-dashboard').empty();
	if (mails.length == 0) {
		$('.mailbox-dashboard').append('<tr><td colspan="6" style="text-align: center; font-style: italic;">' + _('All e-mails handled') + '</td></tr>');
	} else {
		for(var i in mails) {
			var m = mails[i];

			var tdAnswered    = $('<td />');
			if (m.answered) {
				tdAnswered.append('<span class="fa fa-reply" />');
			}
			
			var tdMailboxName = $('<td />');
			tdMailboxName.text( m.mailbox_name );
			
			var tdFromName    = $('<td />');
			tdFromName.text( m.from_name );
			
			var tdSubject     = $('<td />');
			tdSubject.text( m.subject );
			
			var tdAction      = $('<td />');
			tdAction.text( m.action );
			
			var tdDate        = $('<td style="white-space: nowrap;" />');
			tdDate.text(format_datetime(str2datetime(m.date), {skipSeconds: true}));

			var tr = $('<tr />');
			tr.addClass('clickable');
			tr.addClass(m.seen ? 'seen' : 'unseen');
			tr.data('email-id', m.email_id);
			tr.click(function() {
				mailbox_showEmail($(this).data('email-id'));
			});
			
			
			tr.append(tdAnswered);
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


