
<?php if (count($filetemplates)) : ?>
<div class=" js-menu-container">
	<ul id="menu" class="menu">
		<li><div><?= t('New document') ?></div>
			<ul>
				<?php foreach($filetemplates as $ft) : ?>
				<li>
					<div><a href="javascript:void(0);" onclick="filesync_createDocument(<?= esc_json_attr($ft->getId()) ?>);"><?= esc_html($ft->getName()) ?></a></div>
				</li>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>
<?php endif; ?>

<div class="clear" style="height: 10px;"></div>


<div id="archive-customer-index-table"></div>

<script>
<?= $archiveCustomerIndexTable->render() ?>
</script>


