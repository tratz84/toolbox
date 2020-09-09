
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=storefile&id='.$store->getStoreId()) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$form->getWidgetValue('store_file_id')) ?>" target="_blank" class="fa fa-download"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<h1>Geschiedenis bestand</h1>
</div>

<div>
	<?= $form->render() ?>
</div>

<div class="clear"></div>
<br/>

<table class="list-widget">

	<thead>
		<tr>
<!-- 			<th>Id</th> -->
			<th>Bestandsgrootte</th>
			<th>md5sum</th>
			<th>Revisie</th>
			<th>Encrypted</th>
			<th>Laatst gewijzigd</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($revisions as $r) : ?>
		<tr>
<!-- 			<td><?= $r->getStoreFileRevId() ?></td> -->
			<td><?= $r->getFilesize() ?></td>
			<td><?= $r->getMd5sum() ?></td>
			<td><?= $r->getRev() ?></td>
			<td><?= $r->getEncrypted() ? 'Ja' : 'Nee' ?></td>
			<td><?= $r->getLastmodifiedFormat('d-m-Y H:i:s') ?></td>
			<td class="td-actions">
				<a href="<?= appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$storeFile->getStoreFileId().'&rev='.$r->getRev()) ?>" target="_blank" class="fa fa-download"></a>
				<a href="<?= appUrl('/?m=filesync&c=storefile&a=delete&store_file_rev_id='.$r->getStoreFileRevId()) ?>" class="fa fa-trash"></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>



