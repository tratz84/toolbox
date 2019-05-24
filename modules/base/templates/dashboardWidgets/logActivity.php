

<div class="widget-title">
	<?= t('Recent log activity') ?>
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th><?= t('User') ?></th>
			<th><?= t('Customer') ?></th>
			<th><?= t('Description') ?></th>
			<th><?= t('Run on') ?></th>
		</tr>
	</thead>
	<tbody>
    	<?php foreach($activities as $a) : ?>
    	<tr class="clickable" title="<?= esc_attr($a->getUsername()) ?>" onclick="show_popup(appUrl('/?m=base&c=report/activityReport&a=popup&id=<?= $a->getActivityId() ?>'))">
    		<td><?= esc_html($a->getUsername()) ?></td>
    		<td>
    			<?= esc_html($a->getField('company_name')) ?>
    			<?= esc_html(format_personname($a)) ?>
    		</td>
    		<td><?= esc_html($a->getShortDescription()) ?></td>
    		<td><?= $a->getCreatedFormat() ?></td>
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
