
<script src="<?= BASE_HREF ?>lib/chart.js/Chart.min.js"></script>
<script src="<?= BASE_HREF ?>?mpf=/module/report/js/chartjs-helper.js?v=<?= filemtime(module_file('report', 'public/js/chartjs-helper.js')) ?>"></script>


<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=report&c=dashboard/list') ?>" class="fa fa-chevron-circle-left"></a>
		
		<?php if ($reportDashboard->isNew() == false) : ?>
			<a href="<?= appUrl('/?m=report&c=dashboard/edit&a=delete&id='.$reportDashboard->getReportDashboardId()) ?>"
				data-confirmation-message="Weet je zeker dat je deze rapportage wilt verwijderen?" 
				class="fa delete fa-trash"></a>
			<a href="<?= appUrl('/?m=report&c=dashboard/view&show_edit=1&id='.$reportDashboard->getReportDashboardId())?>" 
				class="fa fa-eye"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" id="dashboard-settings-click" class="fa fa-cog"></a>
		<a href="javascript:void(0);" class="fa submit-form fa-save"></a>
	</div>
	
	<h1>Rapportage dashboard bewerken</h1>
</div>

<?= $form->render() ?>

<div class="clear"></div>
<hr />



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


handle_deleteConfirmation();

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
		
// 		evt.preventDefault();
	});


	var formClasses = <?= json_encode($formClasses) ?>;
	$('.widget-container-forms span.value').append('<ul class="report-forms" />');
	for(var formClass in formClasses) {
		var li = $('<li />');
		li.text( formClasses[formClass].description );
		$(li).data( 'formClass', formClass );
		$(li).data( 'widgetCodes', formClasses[formClass].widgetCodes);
		$(li).hide();
		
		$('.report-forms').append( li );
	}
	$('ul.report-forms').sortable();


	dash = new Dashboard( '#grid-report', dwc );
	
});


/**
 * show/hide form order
 */
$(window).on( 'dashboard-dashboard-rendered', function() {
	$('ul.report-forms li').hide();

	console.log('wel hier');
	$('#grid-report .widget-item').each(function(index, node) {
		var wc = $(node).data('widgetCode');

		$('ul.report-forms li').each(function(index2, node2) {
			var wcs = $(node2).data( 'widgetCodes' );
			if ( wcs.indexOf( $(node).data('widgetCode') ) != -1 )
				$(node2).show();
		});
	});
	
});




</script>


