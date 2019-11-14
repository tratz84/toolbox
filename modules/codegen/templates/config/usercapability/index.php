
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=codegen&c=config/usercapability&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>User capabilities</h1>
</div>



<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($capabilities as $c) : ?>
		<tr class="clickable" onclick="row_Click(this);" data-module-name="<?= esc_attr($c['name']) ?>">
			<td><?= esc_html($c['name']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>


<script>

function row_Click(tr) {
	var moduleName = $(tr).data('module-name');

	window.open(appUrl('/?m=codegen&c=config/usercapability&a=edit&mod=' + moduleName), '_self');
}

</script>

