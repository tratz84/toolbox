

<?php if (isset($error)) : ?>
	<?= esc_html($error) ?>
<?php else : ?>

    <?php if (in_array($file_extension, ['pdf'])) : ?>
    	<embed src="<?= esc_attr($file_url) ?>" width="100%" height="600" type="application/pdf">
    <?php elseif (isset($url_pdf_preview)) : ?>
    	<embed src="<?= esc_attr($url_pdf_preview) ?>" width="100%" height="600" type="application/pdf">
    <?php endif; ?>
	<div style="max-width: 100%; margin: 15px auto 0; text-align: left;">
		<a href="<?= esc_attr($download_url) ?>" target="_blank"><?= esc_html($filename) ?></a>
	</div>
    

    <script>
    
    var storeFileData = <?= json_encode($storeFileData) ?>;
    
    $(window).trigger( 'filesync-file-select', [ storeFileData ] );
    
    </script>


<?php endif; ?>