
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	
    <h1>Debug info</h1>
</div>


<b>Server</b>
<pre><?= esc_html(var_export(get_var('server')))?></pre>

<b>GET</b>
<pre><?= esc_html(var_export(get_var('get')))?></pre>

<b>POST</b>
<pre><?= esc_html(var_export(get_var('post')))?></pre>


<b>Events</b>
<?php if (get_var('eventbus') && is_array(get_var('eventbus'))) : ?>
	<table>
		<thead>
			<tr>
				<th>Nr</th>
				<th>Method</th>
				<th>Url</th>
				<th>Module</th>
				<th>Action</th>
				<th>Message</th>
			</tr>
		</thead>
		<tbody>
		<?php $no = 1 ?>
		<?php foreach(get_var('eventbus') as $e) : ?>
		<tr>
			<td><?= $no++ ?></td>
			<td><?= esc_html($e['method']) ?></td>
			<td><?= esc_html($e['url']) ?></td>
			<td><?= esc_html($e['moduleName']) ?></td>
			<td><?= esc_html($e['actionName']) ?></td>
			<td><?= esc_html($e['message']) ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
No events
<?php endif; ?>

