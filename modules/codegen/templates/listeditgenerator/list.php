

<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=codegen&c=listeditgenerator') ?>" class="fa fa-plus"></a>
		
	</div>

	<h1>Forms</h1>
</div>


<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
			<th>File</th>
			<th>Description</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($lists as $li) : ?>
		<tr class="clickable" onclick="window.open( appUrl('/?m=codegen&c=listeditgenerator&fm=<?= $li['module'] ?>&ff=' + <?= esc_attr(json_encode(urlencode($li['file']))) ?>), '_self' );">
			<td><?= esc_html($li['module']) ?></td>
			<td><?= esc_html($li['file']) ?></td>
			<td><?= esc_html($li['short_description']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	

</table>
