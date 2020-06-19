

<?php if ($calendar == null) : ?>

<i>Geen kalender actief</i>

<?php else : ?>




<script src="<?= BASE_HREF ?>js/calendar/calendarController.js"></script>

<input type="hidden" id="calendarId" value="<?= $calendar->getCalendarId() ?>" />

<div id="calendar-container"></div>



<script>

var controller = null;

$(document).ready(function() {
	
	controller = new CalendarController('#calendar-container');

	controller.today = <?= json_encode([
	    'week'    => $today->format('W')
	    , 'month' => $today->format('n')
	    , 'year'  => $today->format('Y')
    ]) ?>;
	
	
	controller.setCalendarId( <?= (int)$calendar->getCalendarId() ?> );
	
	controller.loadData();
});

function calendarReminder_Change(obj) {

	$(obj).find('option').each(function(index, node) {
		$('#reminder-' + $(node).val()).css('display', 'none');
	});

	$('#reminder-' + $(obj).val()).css('display', 'block');
}


</script>


<?php endif; ?>