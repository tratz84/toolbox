
<div class="page-header">
	<h1>Overzicht klanten</h1>
</div>


<table class="list-widget" id="tbl-customers">

	<thead>
		<tr>
			<th>Context name</th>
			<th>Database</th>
			<th>Note</th>
			<th>Actief</th>
		</tr>
	</thead>
	
	<tbody>
    	<?php foreach($customers as $c) : ?>
    	<tr class="clickable" data-context-name="<?= esc_attr($c->getContextName()) ?>">
    		<td><?= esc_html($c->getContextName()) ?></td>
    		<td><?= esc_html($c->getDatabaseName()) ?></td>
    		<td><?= esc_html($c->getNote()) ?></td>
    		<td><?= $c->getActive() ? 'Ja' : 'Nee' ?></td>
    	</tr>
    	<?php endforeach; ?>
	</tbody>

</table>

<script>

$(document).ready(function() {
	$('#tbl-customers tbody tr').click(function() {
		var contextName = $(this).data('context-name');

		show_popup('/admin/?m=admin&c=customer&a=popup_users', {
			data: {
				contextName: contextName
			}
		});
		
	});
});

</script>


