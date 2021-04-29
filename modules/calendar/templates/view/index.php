

<?php if ($calendar == null) : ?>

<i>Geen kalender actief</i>

<?php else : ?>

<?php if (isset($selectCalendar)) : ?>
<div style="position: absolute; right: 10px; margin-top: 16px;">
	<?= $selectCalendar->render() ?>
</div>
<?php endif; ?>



<script src="<?= appUrl('/?mpf=/module/calendar/js/calendarController.js?v='.filemtime(module_file('calendar', 'public/js/calendar.js'))) ?>"></script>

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

	$('[name=cid]').change(function() {
		window.location = appUrl('/?m=calendar&c=view&cid=' + $(this).val());
	});
});

function calendarReminder_Change(obj) {

	$(obj).find('option').each(function(index, node) {
		$('#reminder-' + $(node).val()).css('display', 'none');
	});

	$('#reminder-' + $(obj).val()).css('display', 'block');
}


</script>


<?php endif; ?>