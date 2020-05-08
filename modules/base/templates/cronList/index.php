
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>
	<h1><?= t('Scheduled tasks')?></h1>
</div>


<table class="list-widget">
	<thead>
		<tr>
<!-- 			<th>Naam</th> -->
			<th><?= t('Title') ?></th>
			<th><?= t('Status') ?></th>
			<th><?= t('Run on') ?></th>
			<th><?= t('Started') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($crons as $c) : ?>
		<tr class="clickable" onclick="trCron_Click(<?= $c->getCronId() ?>);">
<!-- 			<td><?= esc_html($c->getCronName()) ?></td> -->
			<td>
				<?php if ($c->getTitle()) : ?>
					<?= esc_html($c->getTitle()) ?>
				<?php else : ?>
					<?= esc_html($c->getCronName()) ?>
				<?php endif; ?>
			</td>
			<td><?= esc_html($c->getLastStatus()) ?></td>
			<td><?= format_date($c->getLastRun(), 'd-m-Y H:i:s') ?></td>
			<td><?= $c->getRunning()?t('Yes'):t('No')?></td>
		</tr>
		<?php endforeach; ?>
		<?php if (count($crons) == 0) : ?>
		<tr>
			<td colspan="5" style="font-style: italic; text-align: center;"><?= t('No scheduled tasks executed') ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>


<script>

function trCron_Click(cron_id) {
	show_popup(appUrl('/?m=base&c=cronList&a=popup&id=' + cron_id));
}

</script>


