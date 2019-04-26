
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	<h1>Activiteit</h1>
</div>


<table class="activity-report-popup">

	<tr class="tr-activity-id">
		<th>Id</th>
		<td><?= $activity->getActivityId() ?></td>
	</tr>
	<tr class="tr-username">
		<th>Gebruiker</th>
		<td>
			<?= esc_html($activity->getUsername()) ?>
		</td>
	</tr>
	<tr class="tr-customer-name">
		<th>Naam</th>
		<td>
			<?= esc_html($activity->getField('company_name')) ?>
			<?= esc_html(format_personname($activity)) ?>
		</td>
	</tr>
	<tr class="tr-ref-object">
		<th>Ref object</th>
		<td><?= esc_html($activity->getRefObject()) ?></td>
	</tr>
	<tr class="tr-ref-id">
		<th>Ref id</th>
		<td><?= esc_html($activity->getRefId()) ?></td>
	</tr>
	<tr class="tr-code">
		<th>Code</th>
		<td><?= esc_html($activity->getCode()) ?></td>
	</tr>
	<tr class="tr-short-description">
		<th>Korte omschrijving</th>
		<td><?= esc_html($activity->getShortDescription()) ?></td>
	</tr>
	<?php if ($activity->getLongDescription()) : ?>
	<tr class="tr-long-description">
		<th>Lange omschrijving</th>
		<td><?= $activity->getLongDescription() ?></td>
	</tr>
	<?php endif; ?>
	<?php if ( $activity->getNote() ) : ?>
	<tr class="tr-note">
		<th>Notitie</th>
		<td><?= esc_html($activity->getNote()) ?></td>
	</tr>
	<?php endif; ?>
	<tr class="tr-created">
		<th>Uitgevoerd op</th>
		<td><?= esc_html($activity->getCreatedFormat()) ?></td>
	</tr>
	<?php if ($changes !== null) : ?>
	<tr class="tr-dump-changes">
		<th>Dump wijzigingen</th>
	</tr>
	<tr class="tr-dump-changes-content">
		<td colspan="2">
		<pre><?= esc_html(var_export($changes, true)) ?></pre>
		</td>
	</tr>
	<?php endif; ?>
	

</table>


