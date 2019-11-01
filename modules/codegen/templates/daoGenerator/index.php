
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>DAO Generator</h1>
</div>


<table class="list-response-table">
	<thead>
		<tr>
			<th>Module name</th>
			<th>Module dir</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach( $modules as $mod ) : ?>
    	<tr class="clickable" onclick="window.location=<?= esc_attr(json_encode(appUrl('/?m=codegen&c=daoGenerator&a=edit&mod='.urlencode($mod['module'])))) ?>">
    		<td><?= esc_html($mod['module']) ?></td>
    		<td><?= esc_html($mod['module_dir']) ?></td>
    	</tr>
    	<?php endforeach; ?>
	</tbody>
</table>


