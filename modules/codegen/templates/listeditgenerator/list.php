

<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=codegen&c=listeditgenerator') ?>" class="fa fa-plus"></a>
		
	</div>

	<h1>ListEdit</h1>
</div>


<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
			<th>File</th>
			<th>Description</th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($lists as $li) : ?>
		<tr class="clickable" onclick="window.open( appUrl('/?m=codegen&c=listeditgenerator&fm=<?= $li['module'] ?>&ff=' + <?= esc_attr(json_encode(urlencode($li['file']))) ?>), '_self' );">
			<td><?= esc_html($li['module']) ?></td>
			<td><?= esc_html($li['file']) ?></td>
			<td><?= esc_html($li['short_description']) ?></td>
			<td class="actions">
				<a href="<?= appUrl('/?m=codegen&c=listeditgenerator&a=delete&fm='.urlencode($li['module']).'&ff='.urlencode($li['file'])) ?>" class="fa fa-remove delete"></a>
			</td>
			
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script>

$(document).ready(function() {
	handle_deleteConfirmation();
});

</script>


