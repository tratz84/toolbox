
<hr/>

<h2>Data sample</h2>
<table class="list-response-table">

	<thead>
		<tr>
			<th width="5"></th>
			<?php foreach($sheet->getRow(0) as $col) : ?>
			<th><?= esc_html($col) ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tbody>
		<?php for($x=1; $x < 5 && $x < $sheet->getRowCount(); $x++) : ?>
		<?php $row = $sheet->getRow($x) ?>
		<tr>
			<td><input type="radio" name="sample_row" value="<?= $x ?>" <?= $x==1?'checked=checked':'' ?> /></td>
			
			<?php foreach($row as $col) : ?>
			<td><?= esc_html($col) ?></td>
			<?php endforeach; ?>
		</tr>
		<?php endfor; ?>
	</tbody>

</table>

