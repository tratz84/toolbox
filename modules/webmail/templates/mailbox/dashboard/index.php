

<div class="widget-title">
	<div class="toolbox">
		<a href="javascript:void(0);" onclick="show_popup(appUrl('/?m=webmail&c=mailbox/dashboard&a=settings'));" class="fa fa-cog"></a>
	</div>

	<?= t('Recent e-mails') ?>
</div>

<?php if (isset($listResponse)) : ?>

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
		<tbody>
        	<?php foreach($listResponse->getObjects() as $obj) : ?>
        	<tr class="clickable <?= $obj['seen'] ? 'seen':'unseen'?>" data-email-id="<?= esc_attr($obj['email_id']) ?>" onclick="mailbox_showEmail($(this).data('email-id'));">
        		<td>
        			<?php if ($obj['answered']) : ?>
        			<span class="fa fa-reply"></span>
        			<?php endif; ?>
        		<td><?= esc_html($obj['mailbox_name']) ?></td>
        		<td><?= esc_html($obj['from_name']) ?></td>
        		<td><?= esc_html($obj['subject']) ?></td>
        		<td><?= esc_html($obj['action']) ?></td>
        		<td><?= format_date($obj['date'], 'd-m-Y H:i:s') ?></td>
        	</tr>
        	<?php endforeach; ?>
        	<?php if (count($listResponse->getObjects()) == 0) : ?>
        	<tr>
        		<td colspan="6" style="text-align: center; font-style: italic;">
        			<?= t('All e-mails handled') ?>
        		</td>
        	</tr>
        	<?php endif; ?>
        </tbody>
	</table>
	
<?php else : ?>
	Error: <?= esc_html($error) ?>
<?php endif; ?>


<script>

function mailbox_showEmail(email_id) {
	window.open( appUrl('/?m=webmail&c=mailbox/search#email_id=' + email_id), '_self' );
}

</script>


