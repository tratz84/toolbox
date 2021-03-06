
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=project&c=project') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Project toevoegen</h1>
    <?php else : ?>
    <h1>Project bewerken</h1>
    <?php endif; ?>
</div>


<?php
    $tabContainer = generate_tabs('project', 'project-edit', $form);
    $tabContainer->addTab('Projectgegevens', $form->render(), 0);
    print $tabContainer->render();
?>




<script>

$(document).ready(function() {

	$('[name=project_billable_type]').change(function() {
		handle_projectHourWidget();
	});
	
	handle_projectHourWidget();
});


function handle_projectHourWidget() {
	if ($('[name=project_billable_type]:checked').val() == 'fixed') {
		$('.project-hours-widget').show();
	} else {
    	$('.project-hours-widget').hide();
	}
}

</script>

