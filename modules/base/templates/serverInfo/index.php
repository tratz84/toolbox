
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Server info') ?></h1>
</div>



<table id="server-info-container" class="list-response-table">
	<thead>
		<tr>
			<th>Description</th>
			<th>Value</th>
			<th>Error</th>
		</tr>
	</thead>

	<tbody ez-for="info" ez-item="i">
	<tr>
		<td style="width: 50%;" [contentHTML]="i.description"></td>
		<td style="{{i.error?'color: #f00' : '' }}">{{i.value}}</td>
		<td style="width: 30%;">{{i.error == null ? '' : i.error}}</td>
	</tr>
	</tbody>

</table>


<script>

let s = new EzTemplate('server-info-container');
s.setVar('info', <?= $json_info ?>);
s.update();


</script>


