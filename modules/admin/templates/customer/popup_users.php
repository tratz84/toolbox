


<?php if (isset($json->message)) : ?>

	Bericht: <?= esc_html($json->message) ?>

<?php else : ?>

	<div class="page-header">
		<div class="toolbox">
			<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		</div>
		
		<h1>Overzicht gebruikers in <?= esc_html($contextName) ?></h1>
	</div>
	
	<br/>
	
	<table class="list-widget">
		<thead>
			<tr>
				<th>Gebruikersnaam</th>
				<th>Volledige naam</th>
				<th>Type</th>
			</tr>
		</thead>
		<tbody>
        	<?php foreach($json->users as $u) : ?>
        	<tr class="clickable" data-context-name="<?= esc_attr($contextName) ?>" data-username="<?= esc_attr($u->username) ?>" onclick="admin_autologin( $(this).data('context-name'), $(this).data('username') );">
        		<td><?= esc_html($u->username) ?></td>
        		<td><?= esc_html($u->firstname) ?> <?= esc_html($u->lastname) ?></td>
        		<td><?= esc_html($u->user_type) ?></td>
        	</tr>
        	<?php endforeach; ?>
    	</tbody>
	</table>
<?php endif; ?>



