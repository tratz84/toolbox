
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=codegen&c=generator/indexTable&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>IndexTable pages</h1>
</div>



<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
			<th>Controller name</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($list as $i) : ?>
		<tr class="clickable" onclick="window.location = appUrl('/?m=codegen&c=generator/indexTable&a=edit&fm='+$(this).data('mod')+'&ff='+$(this).data('file'));" data-mod="<?= esc_attr($i['module_name']) ?>" data-file="<?= esc_attr($i['file'])?>">
			<td><?= esc_html($i['module_name']) ?></td>
			<td><?= esc_html($i['controller_name']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

