
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=codegen&c=menugeneratorController&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>List</h1>
</div>



<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($list as $i) : ?>
		<tr class="clickable" onclick="window.location = appUrl('/?m=codegen&c=menugeneratorController&a=edit&mod='+$(this).data('mod'));" data-mod="<?= esc_attr($i['module_name']) ?>">
			<td><?= esc_html($i['module_name']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>


