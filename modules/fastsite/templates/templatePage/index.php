
<link href="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.css" type="text/css" rel="stylesheet" />
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/addon/edit/matchbrackets.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/xml/xml.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/javascript/javascript.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/css/css.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/clike/clike.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/php/php.js"></script>




<div class="page-header">
	<div class="toolbox">
		<a href="/?m=fastsite&c=templateEditor&n=business-casual-gh-pages" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('#frm').submit();" class="fa fa-save"></a>
	</div>

	<h1>Template configureren</h1>
</div>

<form method="post" id="frm" class="form-generator" onsubmit="templatePage_Submit();">

	<div class="widget">
	    <label>Template name</label>
	    <input type="text" name="template_name" value="<?= esc_attr($tpd->getName()) ?>" />
	</div>
    
    <div id="snippet-container"></div>
    <a href="javascript:void(0);" onclick="add_snippet();" class="fa fa-plus"> Snippet toevoegen</a>
    
</form>


<iframe src="<?= BASE_HREF . $file ?>" style="width: 100%; height: 600px;"></iframe>



<script src="<?= BASE_HREF ?>js/TabContainer.js"></script>
<script>
var tc = new TabContainer('#snippet-container');
tc.init();


function add_snippet(opts) {
	opts = opts || {};
	
	var htmlTab = <?= json_encode(get_component('fastsite', 'templatePage', 'snippet')) ?>;
	var t = tc.addTab('snippet1', htmlTab);

	var ta = t.contentContainer.find('textarea');

	CodeMirror.fromTextArea( ta.get(0), {
		lineNumbers: true,
		mode: 'php',
	});
}

add_snippet();

function templatePage_Submit() {
	
}


</script>


