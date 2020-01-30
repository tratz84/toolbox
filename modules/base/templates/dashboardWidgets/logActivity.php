

<div class="widget-title">
	<?= t('Recent log activity') ?>
</div>

<table class="list-widget widget-log-activity" style="width: 100%;">
	<thead>
		<tr>
			<th class="th-user"><?= t('User') ?></th>
			<th class="th-customer"><?= t('Customer') ?></th>
			<th class="th-description"><?= t('Description') ?></th>
			<th class="th-run-on"><?= t('Run on') ?></th>
		</tr>
	</thead>
	<tbody>
    	<?php foreach($activities as $a) : ?>
    	<tr class="clickable" title="<?= esc_attr($a->getUsername()) ?>" onclick="show_popup(appUrl('/?m=base&c=report/activityReport&a=popup&id=<?= $a->getActivityId() ?>'))">
    		<td class="td-user"><?= esc_html($a->getUsername()) ?></td>
    		<td class="td-customer">
    			<?= esc_html($a->getField('company_name')) ?>
    			<?= esc_html(format_personname($a)) ?>
    		</td>
    		<td class="td-description"><?= esc_html($a->getShortDescription()) ?></td>
    		<td class="td-run-on"><?= $a->getCreatedFormat() ?></td>
    	</tr>
    	<?php endforeach; ?>
    	<tr>
    		<td colspan="4" class="no-records">
            	<?php if (count($activities)==0) : ?>
                <?= t('No log-activities registered') ?>
                <?php endif; ?>
    		</td>
    	</tr>
    </tbody>

</table>
