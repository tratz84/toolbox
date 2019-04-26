
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" id="dashboard-settings-click"
			class="fa fa-cog"></a>
	</div>

	<h1>Dashboard</h1>
</div>


<div class="dashboard-widgets">
	<div id="grid-stack" class="grid-stack"></div>
</div>


<script src="<?= BASE_HREF ?>lib/lodash/lodash.fp.min.js?t=1538721029369"></script>
<script src="<?= BASE_HREF ?>lib/lodash/lodash.core.min.js?t=1538721029369"></script>
<script src="<?= BASE_HREF ?>lib/lodash/lodash.min.js?t=1538721029369"></script>

<script src="<?= BASE_HREF ?>lib/gridstack/gridstack.all.js?t=1538721029369"></script>
<link href="<?= BASE_HREF ?>lib/gridstack/gridstack.min.css?t=1538721029369"
	rel="stylesheet" type="text/css" />


<script
	src="<?= BASE_HREF ?>js/dashboard/dashboard.js?v=<?= filemtime(WWW_ROOT.'/js/dashboard/dashboard.css') ?>"></script>
<link
	href="<?= BASE_HREF ?>js/dashboard/dashboard.css?v=<?= filemtime(WWW_ROOT.'/js/dashboard/dashboard.css') ?>"
	rel="stylesheet" type="text/css" />


<script type="text/javascript">



var dash = null;
var dwc = <?= json_encode($dwc) ?>;

//{"widgets":[{"code":"projectRecentHours","name":"Laatste uren projecten","description":"Overzicht van de laatst geregistreerde uren","ajaxUrl":"/project/widget/list.do"},{"code":"calendarUpcoming","name":"Kalender - aankomende afspraken","description":"Toont de opkomende afspraken voor komende 10 werkdagen","ajaxUrl":"/calendar/widget/upcoming.do"},{"code":"todo","name":"ToDo","description":"Overzicht te doen","ajaxUrl":"/calendar/widget/todo.do"},{"code":"webmailLastmail","name":"Laatste 20 e-mails","description":"Overzicht laatste 20 e-mails","ajaxUrl":"/webmail/widget/lastmail.do"},{"code":"lastSupportEntries","name":"Supportaanvragen","description":"Overzicht van laatste 20 supportaanvragen","ajaxUrl":"/support/widget/lastActivities.do"},{"code":"invoicesOutstanding","name":"Openstaande facturen","description":"Overzicht openstaande facturen","ajaxUrl":"/invoice/widget/invoicesoutstanding.do"},{"code":"lastRentalEntries","name":"Laatste wijzigingen verhuur","description":"Overzicht van de laatste wijzigingen in de verhuur","ajaxUrl":"/rental/widget/lastActivityEntries.do"}],"userWidgets":{"todo":{"x":3,"width":3,"y":0,"height":3},"webmailLastmail":{"x":6,"width":6,"y":0,"height":6},"projectRecentHours":{"x":0,"width":6,"y":3,"height":3},"lastSupportEntries":{"x":6,"width":6,"y":6,"height":3},"invoicesOutstanding":{"x":0,"width":6,"y":6,"height":3},"calendarUpcoming":{"x":0,"width":3,"y":0,"height":3}}};

$(document).ready(function() {
	
	dash = new Dashboard( '#grid-stack', dwc );
	
});


</script>
