
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1><?= t('Overview') ?></h1>
</div>


<table class="list-widget">

	<thead>
		<tr>
			<th><?= t('Message') ?></th>
			<th><?= t('Error message') ?></th>
			<th><?= t('Status') ?></th>
			<th><?= t('Executed on') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($cronRuns as $cr) : ?>
		<tr>
			<td><?= esc_html($cr->getMessage()) ?></td>
			<td><?= esc_html($cr->getError()) ?></td>
			<td><?= esc_html($cr->getStatus()) ?></td>
			<td><?= esc_html($cr->getCreatedFormat()) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

