

<div class="widget-title">
	<?= t('Upcoming agenda items') ?>
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th><?= t('Description') ?></th>
			<th style="width: 150px;"><?= t('Date / time') ?></th>
			<th style="width: 50px;"><?= t('Day') ?></th>
		</tr>
	</thead>
	
	<tbody>
	<?php if (count($items) == 0) : ?>
		<tr>
			<td colspan="3" style="text-align: center; font-style: italic;"><?= t('No upcoming agenda items') ?></td>
		</tr>
	<?php else : ?>
		<?php foreach($items as $i) : ?>
		<tr class="clickable <?= $i->getCancelled() ? 'item cancelled' : '' ?>" onclick="<?= esc_attr("show_popup(appUrl('/?m=calendar&c=view&a=edit'), { data: { calendar_item_id: ".$i->getId() . ", readonly: true }})") ?>">
			<td>
				<?php if ($i->getCustomerName()) : ?>
				<span style="font-weight: 600;"><?= esc_html($i->getCustomerName()) ?></span>:
				<?php endif; ?>
				<?= esc_html($i->getDescription()) ?>
			</td>
			<td>
				
				<?= $i->getStartDateFormat() ?>
				<?php if ($i->getStartTime()) : ?>
					<?= $i->getStartTime() ?>
				<?php endif; ?>
			</td>
			<td style="font-size: 10px;">(<?= strtolower(t('dayno.'.$i->getStartDateFormat('N'))) ?>)</td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	
	</tbody>
	
	
</table>

