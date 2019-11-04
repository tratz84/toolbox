
<div class="page-header">
	
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	
	<h1>Select widget</h1>
</div>



<ul>
    <?php foreach($formWidgets as $w) : ?>
	<li>
		<a href="javascript:void(0);" onclick="add_widget( $(this).data('widget') );" data-widget="<?= esc_attr(json_encode($w)) ?>"><?= esc_html($w['label']) ?></a>
	</li>    
    <?php endforeach; ?>
</ul>
