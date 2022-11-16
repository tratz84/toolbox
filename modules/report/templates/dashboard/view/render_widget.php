
<div class="widget-title">
	<?= $widget->getReportTitle() ?>
</div>

<div id="<?= $widget ? $widget->getReportCode() : 'not-found' ?>-actions-table-container" class="dashboard-widget-content">


<?php if ($widget) : ?>
	<?= $widget->render() ?>
<?php else : ?>
	Widget not found
<?php endif; ?>


</div>