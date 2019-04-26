


<?= $form->render() ?>

<hr/>


<table class="list-widget">

	<thead>
		<tr>
			<th>Gebruiker</th>
			<th>Klant</th>
			<th>Project</th>
			<th>Omschrijving</th>
			<th>Start</th>
			<th>Eind</th>
			<th>Duur</th>
			<th>Declarabel</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<?php $totalMinutes = 0;?>
		<?php foreach ($lrHours->getObjects() as $ph) : ?>
		<tr class="clickable" onclick="window.open(appUrl('/?m=project&c=projectHour&a=edit&project_hour_id=<?= $ph['project_hour_id'] ?>'), '_blank');">
			<td>
				<input type="hidden" class="project-hour-id" value="<?= esc_attr($ph['project_hour_id']) ?>" />
				<?= esc_html($ph['username']) ?>
			</td>
			<td>
				<?php if ($ph['company_name']) : ?>
					<?= esc_html($ph['company_name']) ?>
				<?php else : ?>
					<?= esc_html(format_personname($ph)) ?>
				<?php endif; ?>
			</td>
			<td><?= esc_html($ph['project_name']) ?></td>
			<td><?= esc_html($ph['short_description']) ?></td>
			<td><?= format_datetime($ph['start_time']) ?></td>
			<td><?= format_datetime($ph['end_time']) ?></td>
			<td>
				<?php $totalMinutes += (int)$ph['total_minutes']; ?>
				<?= myround($ph['total_minutes']/60,2) ?>
			</td>
			<td><?= $ph['declarable'] ? 'Ja' : 'Nee '?></td>
			<td><?= esc_html($ph['status_description']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><b><?= myround($totalMinutes/60, 2) ?></b></td>
			<td></td>
			<td></td>
		</tr>
	</tfoot>

</table>

<div>

	Actie:
	<select name="reportAction">
		<option value="status">Status zetten naar</option>
	</select>
	<select name="projectHourStatusId">
		<option value="">Maak uw keuze</option>
		<?php foreach($hourStatuses as $s) : ?>
		<option value="<?= $s->getProjectHourStatusId() ?>"><?= esc_html($s->getDescription()) ?></option>
		<?php endforeach; ?>
	</select>
	<input type="button" value="Uitvoeren" onclick="actionExecute_Click();" />

</div>



<script>

$(document).ready(function() {
	var frm = $('.form-project-hour-report-form');

	frm.attr('method', 'get');

	frm.find('.submit-container').remove();


	frm.find('input, select').change(function() {
		reloadReport();
	});

	frm.find('[name=start], [name=end]').on('dp.change', function() {
		$(this).data('changed', true);
	});
	frm.find('[name=start], [name=end]').blur(function() {
		if ($(this).data('changed')) {
			reloadReport();
		}
	});
});

function reloadReport() {
	var u = appUrl('/?m=report&c=report&controllerName=project@report/hours');
	var data = serialize2object('#report-html');
	for(var key in data) {
		if (key == 'reportAction' || key == 'projectHourStatusId') {
			continue;
		}
		if (key && data[key]) {
			u += '&' + key + '=' + data[key];
		}
	}

	window.location = u;
}


function actionExecute_Click() {
	if ($('select[name=projectHourStatusId]').val() == '') {
		showAlert('Fout', 'Geen nieuwe status gekozen');
		return;
	}

	var ids = [];
	$('.project-hour-id').each(function(index, node) {
		ids.push( $(node).val() );
	});

	var data = { };
	data['reportAction'] = $('select[name=reportAction]').val();
	data['projectHourStatusId'] = $('select[name=projectHourStatusId]').val();
	data['ids'] = ids.join(',');

	formpost('', data);
}





</script>
