

<?php if (isset($error)) : ?>
	<?= esc_html($error) ?>
<?php else : ?>

    <?php if (in_array($file_extension, ['pdf', 'xls', 'xlsx', 'doc', 'docx'])) : ?>
    
    	<embed src="<?= esc_attr($file_url) ?>" width="100%" height="600" type="application/pdf">
    	
    <?php endif; ?>
    

    <script>
    
    var storeFileData = <?= json_encode($storeFileData) ?>;
    
    $(window).trigger( 'filesync-file-select', [ storeFileData ] );
    
    </script>


<?php endif; ?>