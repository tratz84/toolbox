
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=codegen&c=formgenerator') ?>" class="fa fa-plus"></a>
		
	</div>

	<h1>Forms generator</h1>
</div>


<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
			<th>Name</th>
			<th>File</th>
			<th>Description</th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($forms as $f) : ?>
		<tr class="clickable" onclick="window.open( appUrl('/?m=codegen&c=formgenerator&fm=<?= $f['module'] ?>&ff=' + <?= esc_attr(json_encode(urlencode($f['file']))) ?>), '_self' );">
			<td><?= esc_html($f['module']) ?></td>
			<td><?= esc_html($f['form_name']) ?></td>
			<td><?= esc_html($f['file']) ?></td>
			<td><?= esc_html($f['short_description']) ?></td>
			<td class="actions">
				<a href="<?= appUrl('/?m=codegen&c=formgenerator&a=delete&fm='.urlencode($f['module']).'&ff='.urlencode($f['file'])) ?>" class="fa fa-remove delete"></a>
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

