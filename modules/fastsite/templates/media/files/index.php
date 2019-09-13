
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=media/upload') ?>" class="fa fa-upload"></a>
	</div>

	<h1>Media</h1>
</div>



<div class="filelist-container">
	
	<?php foreach($files as $f) : ?>
	<div class="file">
		<?php $p = module_file('fastsite', 'public/images/icons/'.file_extension($f).'.png') ? file_extension($f) : '_blank' ?>
		<a href="<?= appUrl('/?m=fastsite&c=media/info&f='.urlencode($f)) ?>">
    		<img src="<?= BASE_HREF ?>module/fastsite/images/icons/<?= $p ?>.png" />
    		<span class="label"><?= esc_html($f) ?></span>
    	</a>
	</div>
	<?php endforeach; ?>
	
	<?php if (count($files) == 0) : ?>
	<div style="font-style: italic; margin: 20px 0; text-align: center;">
		<?= t('No files found') ?>
	</div>
	<?php endif; ?>
	
</div>


