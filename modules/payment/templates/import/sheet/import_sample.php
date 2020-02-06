
<hr/>

<h2>Data sample</h2>
<table class="list-response-table">

	<thead>
		<tr>
			<?php foreach($sheet->getRow(0) as $col) : ?>
			<th><?= esc_html($col) ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tbody>
		<?php for($x=1; $x < 5 && $x < $sheet->getRowCount(); $x++) : ?>
		<?php $row = $sheet->getRow($x) ?>
		<tr>
			<?php foreach($row as $col) : ?>
			<td><?= esc_html($col) ?></td>
			<?php endforeach; ?>
		</tr>
		<?php endfor; ?>
	</tbody>

</table>

