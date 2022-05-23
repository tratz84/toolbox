

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=report&c=dashboard/list') ?>" class="fa fa-chevron-circle-left"></a>
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



var dash = null;
var dwc = <?= json_encode($dwc) ?>;

$(document).ready(function() {
	
	dash = new Dashboard( '#grid-report', dwc );


	$('.form-report-dashboard-form').on('submit', function(evt) {
		
		var data = {};
		
		for(var x=0; x < dash.getGrid().grid.nodes.length; x++) {
			var item = dash.getGrid().grid.nodes[x];
			
			var widgetCode = $(item.el).data('widgetCode');
			
			data[widgetCode] = {};
			data[widgetCode]['x']      = item.x;
			data[widgetCode]['y']      = item.y;
			data[widgetCode]['width']  = item.width;
			data[widgetCode]['height'] = item.height;
		}

		$('[name=grid_data]').val( JSON.stringify( data ) );
		
// 		evt.preventDefault();
	});
	
});


</script>


