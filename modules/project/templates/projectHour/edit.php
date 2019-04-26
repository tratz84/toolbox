
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=project&c=projectHour'.($project_id?'&project_id='.$project_id:'').($company_id?'&company_id='.$company_id:'').($person_id?'&person_id='.$person_id:'')) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Uur toevoegen</h1>
    <?php else : ?>
    <h1>Uur bewerken</h1>
    <?php endif; ?>
</div>


<?php
    $tabContainer = generate_tabs('project', 'project-hour-edit', $form);
    $tabContainer->addTab('Uurregistratie', $form->render(), 0);
    print $tabContainer->render();
?>


<script>

$(document).ready(function() {
	$('[name=registration_type]').change(function() {
		toggleFields();
	});

	toggleFields();
});

function toggleFields() {
	var rt = $('[name=registration_type]:checked').val();

	if (rt == 'from_to') {
		$('.widget-end-time').show();
		$('.duration-widget').hide();
	}
	if (rt == 'duration') {
		$('.widget-end-time').hide();
		$('.duration-widget').show();
	}
	
}

</script>
