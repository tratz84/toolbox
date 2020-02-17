
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


	var st_incMinute = $('<a href="javascript:void(0);" class="fa fa-plus"></a>');
	st_incMinute.click(function() { adjust_time('[name=start_time]', '+', 'minute'); });
	
	var st_incHour = $('<a href="javascript:void(0);" class="fa fa-plus"></a>');
	st_incHour.click(function() { adjust_time('[name=start_time]', '+', 'hour'); });
	
	var st_decMinute = $('<a href="javascript:void(0);" class="fa fa-minus"></a>');
	st_decMinute.click(function() { adjust_time('[name=start_time]', '-', 'minute'); });

	var st_decHour = $('<a href="javascript:void(0);" class="fa fa-minus dec-hour"></a>');
	st_decHour.click(function() { adjust_time('[name=start_time]', '-', 'hour'); });
	
	st_incHour.insertAfter($('[name=start_time]'));
	st_incMinute.insertAfter($('[name=start_time]'));
	st_decMinute.insertAfter($('[name=start_time]'));
	st_decHour.insertAfter($('[name=start_time]'));




	var et_incMinute = $('<a href="javascript:void(0);" class="fa fa-plus"></a>');
	et_incMinute.click(function() { adjust_time('[name=end_time]', '+', 'minute'); });
	
	var et_incHour = $('<a href="javascript:void(0);" class="fa fa-plus"></a>');
	et_incHour.click(function() { adjust_time('[name=end_time]', '+', 'hour'); });
	
	var et_decMinute = $('<a href="javascript:void(0);" class="fa fa-minus"></a>');
	et_decMinute.click(function() { adjust_time('[name=end_time]', '-', 'minute'); });

	var et_decHour = $('<a href="javascript:void(0);" class="fa fa-minus dec-hour"></a>');
	et_decHour.click(function() { adjust_time('[name=end_time]', '-', 'hour'); });
	
	et_incHour.insertAfter($('[name=end_time]'));
	et_incMinute.insertAfter($('[name=end_time]'));
	et_decMinute.insertAfter($('[name=end_time]'));
	et_decHour.insertAfter($('[name=end_time]'));
	
});

function adjust_time(field, method, type) {
	var val = $(field).val();

	if ($(field).attr('name') == 'end_time' && val == '') {
		val = $('[name=start_time]').val();
	}
	

	var date = <?= json_encode(date('d-m-Y')) ?>;
	var time = '07:00';

	if (val == '') {
		$(field).val( date + ' ' + time );
		return;
	}
	
	if (val != '') {
		var tokens = val.split(' ');

		if (tokens.length == 2) {
    		date = tokens[0];
    		time = tokens[1];
		}
	}

	var time_tokens = time.split(':');
	var hour   = parseInt( time_tokens[0] );
	var minuts = parseInt( time_tokens[1] );

	if (type == 'hour') {
		if (method == '+') {
			hour++;
		} else {
			hour--;
		}
	}
	
	if (type == 'minute') {
		if (method == '+') {
    		minuts += 15;
    		if (minuts >= 60) {
    			minuts = 0;
    			hour++;
    		}
		} else {
			minuts -= 15;
			
			if (minuts < 0) {
				hour--;
				minuts = 45;
			}
		}
	}

	if (hour > 23) {
		hour = 23;
		minuts = 45;
	}
	if (hour < 0) {
		hour = 0;
		minuts = 0;
	}

	time = (hour < 10 ? '0' + hour : hour) + ':' + (minuts < 10 ? '0'+minuts : minuts);
	
	$(field).val(date + ' ' + time);
}


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
