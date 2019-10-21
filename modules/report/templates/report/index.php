
<div class="report-container <?= implode(' ', $divReportClasses) ?>">

<div class="page-header">
	
	<div class="toolbox list-toolbox">
    	<?php if (isset($report) && $report->getExcelUrl()) : ?>
		<a href="javascript:void(0);" onclick="exportReportToXls();" class="fa fa-file-excel-o"></a>
		<?php endif; ?>
	</div>
	
    <h1>
    	<?= t('report.Reports') ?>
    	<?php if (isset($report)) : ?>
    	- <?= esc_html($report->getName()) ?>
    	<?php endif; ?>
    </h1>
</div>

<div class="report-selection">
    <select name="controllerName" onchange="window.location=appUrl('/?m=report&c=report&controllerName=' + this.value)">
    	<option value="">Kies een rapportage</option>
    	<?php foreach($rml->getMenuItems() as $mi) : ?>
    	<option value="<?= esc_attr($mi->getModule() . '@' . $mi->getControllerName()) ?>" <?= (isset($report) && $mi->getControllerName() == $report->getControllerName()) ? 'selected=selected' : '' ?>>
    		<?= esc_html($mi->getName()) ?>
    	</option>
    	<?php endforeach; ?>
    </select>
    
    <hr/>
</div>

<div id="report-html">
<?php if (isset($reportHtml)) : ?>
	<?= $reportHtml ?>
<?php endif; ?>
</div>

</div>

<script>

<?php if (isset($report)) : ?>
var xlsExportUrl = <?= json_encode($report->getExcelUrl()) ?>;

function exportReportToXls() {
	var data = serialize2object('#report-html');
	
	formpost(xlsExportUrl, data);
}
<?php endif; ?>

</script>





