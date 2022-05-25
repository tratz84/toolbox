
<script src="<?= BASE_HREF ?>lib/chart.js/Chart.min.js"></script>
<script src="<?= BASE_HREF ?>?mpf=/module/report/js/chartjs-helper.js?v=<?= filemtime(module_file('report', 'public/js/chartjs-helper.js')) ?>"></script>


<div class="page-header">
	<div class="toolbox">
		<?php if (get_var('show_edit') && hasCapability('report', 'edit-dashboard')) : ?>
		<a href="<?= appUrl('/?m=report&c=dashboard/edit&id='.$report->getReportDashboardId()) ?>" class="fa fa-pencil"></a>
		<?php endif; ?>
		
		<a href="<?= appUrl('/?m=report&c=dashboard/list') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= esc_html($report->getName()) ?></h1>
</div>

<div class="dashboard-form">
<?php foreach($forms as $f) : ?>
	<div class="report-form report-form-<?= slugify(get_class($f)) ?>">
		<?= $f->render();?>
	</div>
<?php endforeach; ?>
</div>

<hr class="clear" />




<div class="dashboard-widgets">
	<div id="grid-report" class="grid-stack grid-report"></div>
</div>


<script src="<?= BASE_HREF ?>lib/lodash/lodash.fp.min.js?t=1538721029369"></script>
<script src="<?= BASE_HREF ?>lib/lodash/lodash.core.min.js?t=1538721029369"></script>
<script src="<?= BASE_HREF ?>lib/lodash/lodash.min.js?t=1538721029369"></script>

<script src="<?= BASE_HREF ?>lib/gridstack/gridstack.all.js?t=1538721029369"></script>
<link href="<?= BASE_HREF ?>lib/gridstack/gridstack.min.css?t=1538721029369"
	rel="stylesheet" type="text/css" />


<script
	src="<?= BASE_HREF ?>js/dashboard/dashboard.js?v=<?= filemtime(WWW_ROOT.'/js/dashboard/dashboard.js') ?>"></script>
<link
	href="<?= BASE_HREF ?>js/dashboard/dashboard.css?v=<?= filemtime(WWW_ROOT.'/js/dashboard/dashboard.css') ?>"
	rel="stylesheet" type="text/css" />


<script type="text/javascript">

var dash = null;
var dwc = <?= json_encode($dwc) ?>;

$(document).ready(function() {
	
	$('.form-report-dashboard-form').on('submit', function(evt) {
		// get grid_data
		var grid_data = {};
		for(var x=0; x < dash.getGrid().grid.nodes.length; x++) {
			var item = dash.getGrid().grid.nodes[x];
			
			var widgetCode = $(item.el).data('widgetCode');
			
			grid_data[widgetCode] = {};
			grid_data[widgetCode]['x']      = item.x;
			grid_data[widgetCode]['y']      = item.y;
			grid_data[widgetCode]['width']  = item.width;
			grid_data[widgetCode]['height'] = item.height;
		}

		// form-class order
		var form_classes = [];
		$('ul.report-forms li').each(function(index, node) {
			form_classes.push( $(node).data('formClass') );
		});
		

		var settings = {};
		settings['grid_data'] = grid_data;
		settings['form_classes'] = form_classes;

		$('[name=settings]').val( JSON.stringify( settings ) );
	});


	dash = new Dashboard( '#grid-report', dwc );
	
});





</script>


