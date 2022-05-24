
<div class="page-header">

	<div class="toolbox">
		<?php if (hasCapability('report', 'edit-dashboard')) : ?>
		<a href="<?= appUrl('/?m=report&c=dashboard/edit') ?>" class="fa fa-plus"></a>
		<?php endif; ?>
	</div>

	<h1>Dashboard overzicht</h1>
</div>

<?php if (count($reportDashboards) > 0) : ?>

<div class="col-12 col-lg-6 setting-menu-tag-container no-padding">

    <ul>
    <?php foreach($reportDashboards as $rd) : ?>
    <li>
    	<a href="<?= appUrl('/?m=report&c=dashboard/view&id='.$rd->getReportDashboardId()) ?>"><?= esc_html( $rd->getName() ) ?></a>
		
		<?php if (hasCapability('report', 'edit-dashboard')) : ?>
    	<div class="show-on-hover"><a href="<?= appUrl('/?m=report&c=dashboard/edit&id='.$rd->getReportDashboardId()) ?>" class="fa fa-pencil"></a></div>
    	<?php endif; ?>
    	
    </li>
    <?php endforeach; ?>
    </ul>

</div>
<?php  else : ?>

	<div class="no-results-found">
		<?= t('No dashboards created') ?>
	</div>

<?php endif; ?>

