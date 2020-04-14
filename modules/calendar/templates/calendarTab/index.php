


<div id="calendar-tab-table-container">

	<table class="list-response-table">
		<thead>
			<tr>
				<th><?= t('Title') ?></th>
				<th><?= t('Location') ?></th>
				<th style="width: 145px;"><?= t('Start') ?></th>
				<th style="width: 145px;"><?= t('End') ?></th>
				<th style="width: 90px;"><?= t('Recurrent') ?></th>
				<th style="width: 80px;"><?= t('Action') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php for($x=count($events)-1; $x >= 0; $x--) : ?>
			<?php $e = $events[$x]; ?>
			<tr class="clickable" onclick="show_popup(appUrl('/?m=calendar&c=view&a=edit&readonly=true&calendar_item_id=<?= $e->getId() ?>'))">
				<td>
					<?= esc_html($e->getDescription()) ?>
				</td>
				<td>
					<?= esc_html($e->getLocation()) ?>
				</td>
				<td>
					<?= format_date($e->getStartDate(), 'd-m-Y') ?>
					<?php if ($e->getStartTime()) : ?>
						<?= $e->getStartTime() ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($e->getEndDate()) : ?>
    					<?= format_date($e->getEndDate(), 'd-m-Y') ?>
    					<?php if ($e->getEndTime()) : ?>
    						<?= $e->getEndTime() ?>
    					<?php endif; ?>
    				<?php endif; ?>
				</td>
				<td>
					<?= $e->getRecurrent() ? t('Yes') : t('No') ?>
				</td>
				<td>
					<?= esc_html($e->getItemAction()) ?>
				</td>
			</tr>
			<?php endfor; ?>
			<?php if (count($events) == 0) : ?>
			<tr>
				<td colspan="6" style="font-style: italic; text-align: center;">
					<?= t('No calendar items') ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	
	</table>

</div>

