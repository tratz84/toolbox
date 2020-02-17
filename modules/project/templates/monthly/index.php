
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=project&c=project') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Maandelijks overzicht</h1>
</div>


<div class="overview-selection">
    <?= $selectUser->render() ?> 
    <?= $selectMonth->render() ?>
</div>

<div class="clear"></div>

<hr/>

<?php if ($selected_user_id) : ?>

<table class="monthly-project-hours">
	<tr>
		<th></th>
		<th>Maandag</th>
		<th>Dinsdag</th>
		<th>Woensdag</th>
		<th>Donderdag</th>
		<th>Vrijdag</th>
		<th>Zaterdag</th>
		<th>Zondag</th>
		<th></th>
	</tr>
	<?php $weeks = count($daysPerWeek)/7; ?>
	<?php $totalMinutsMonth = 0; ?>
	<?php for($weekno=0; $weekno < $weeks; $weekno++) : ?>
	<tr>
			<?php
			$row_week_no = null;
			$row_week_no_is_current = false;
			
			 $pos = ($weekno*7);
			 $firstDay = null;
			 for($x=$pos; $x < count($daysPerWeek); $x++) {
			     if ($daysPerWeek[$x] != '-') {
			         $firstDay = $daysPerWeek[$x];
			         break;
			     }
			 }
			 if ($firstDay) {
			     $date = substr($selected_month, 0, 8) . sprintf('%02d', $firstDay);
			     $dt = new DateTime($date, new DateTimeZone('Europe/Amsterdam'));
			     $row_week_no = $dt->format('W');
			     
			     $dt_now = new DateTime(null, new DateTimeZone('Europe/Amsterdam'));
			     $row_week_no_is_current = $dt->format('Y-W') == $dt_now->format('Y-W') ? true : false;
			 }
			?>
		<td class="<?= $row_week_no_is_current ? 'current-week' : '' ?>">
			<?= $row_week_no ?>
		</td>
		<?php $totalMinutsWeek = 0; ?>
		<?php for($weekdayno=0; $weekdayno < 7; $weekdayno++) : ?>
			<?php $pos = ($weekno*7) + $weekdayno ?>
			<?php $dayno = $daysPerWeek[$pos] ?>
			
    		<td class="day <?= date('Y-m') == substr($selected_month, 0, 7) && (int)$dayno == (int)date('d') ? 'current-date' : '' ?>">
    			<?php if ($daysPerWeek[$pos] != '-') : ?>
					<?php $totalMinutsWeek += $hours[$dayno] ?>
					<?php $totalMinutsMonth += $hours[$dayno] ?>
        			
        			<?php $date = substr($selected_month, 0, 8) . sprintf('%02d', $dayno);?>
        			<span class="day-no"><?= $dayno ?></span>
        			
        			<a href="<?= appUrl('/?m=project&c=projectHour&date='.$date)?>" class="hour-count"><?= round($hours[$dayno]/60, 2) ?></a>
    			<?php endif; ?>
    			
    		</td>
		<?php endfor; ?>
		<td class="total-minuts-week">
			<?= round($totalMinutsWeek/60, 2) ?>
		</td>
	</tr>
	<?php endfor; ?>
	<tr class="total-minuts-month">
		<td colspan="8"></td>
		<td class="total-minuts-month"><?= round($totalMinutsMonth/60, 2) ?></td>
	</tr>
</table>


<?php else : ?>

	No user found

<?php endif; ?>



<script>

$(document).ready(function() {
	$('.overview-selection').find('select').change(function() {
		var url = '/?m=project&c=monthly';
		url += '&user_id=' + $('select[name=user_id]').val();
		url += '&month=' + $('select[name=month]').val();

		window.location = appUrl( url );
	});


	var m = $('.overview-selection').find('select[name=month]');

	var anchorPrevMonth = $('<a class="fa fa-angle-left prev-month" href="javascript:void(0);" />');
	anchorPrevMonth.click(function() {
		var opt = $('[name=month]').find('option:selected');
		opt.prev().prop('selected', true);
		opt.trigger('change');
	});
	anchorPrevMonth.insertBefore(m);
	
	var anchorNextMonth = $('<a class="fa fa-angle-right next-month" href="javascript:void(0);" />');
	anchorNextMonth.click(function() {
		var opt = $('[name=month]').find('option:selected');
		opt.next().prop('selected', true);
		opt.trigger('change');
	});
	anchorNextMonth.insertAfter(m);
});


</script>

