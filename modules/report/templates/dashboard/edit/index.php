

<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" id="dashboard-settings-click"
			class="fa fa-cog"></a>
	</div>
	
	<h1>Rapportage dashboard bewerken</h1>
</div>




<div class="dashboard-widgets">
	<div id="grid-report" class="grid-report"></div>
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
	dash.save = function() {
	};
	
	
});


</script>


