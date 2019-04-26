

<div class="widget-title">
	Opkomende agendapunten
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th>Omschrijving</th>
			<th style="width: 150px;">Datum / tijd</th>
			<th style="width: 50px;">Dag</th>
		</tr>
	</thead>
	
	<tbody>
	<?php if (count($items) == 0) : ?>
		<tr>
			<td colspan="3" style="text-align: center; font-style: italic;">Geen opkomende punten</td>
		</tr>
	<?php else : ?>
		<?php foreach($items as $i) : ?>
		<tr class="not-clickable">
			<td><?= esc_html($i->getDescription()) ?></td>
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

