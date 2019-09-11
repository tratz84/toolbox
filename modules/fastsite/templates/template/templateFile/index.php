
<link href="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.css" type="text/css" rel="stylesheet" />
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/addon/edit/matchbrackets.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/xml/xml.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/javascript/javascript.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/css/css.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/clike/clike.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/php/php.js"></script>
<script src="<?= BASE_HREF ?>module/fastsite/lib/templateFile/SnippetController.js"></script>



<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=template/fileEditor&n='.urlencode($template)) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('#frm').submit();" class="fa fa-save"></a>
	</div>

	<h1>Template configureren</h1>
</div>

<form method="post" id="frm" class="form-generator" onsubmit="templateFile_Submit();">

	<input type="hidden" id="template_name" name="template_name" value="<?= esc_attr($template) ?>" />

	<div class="widget">
	    <label>Template file</label>
	    <?= esc_html($file) ?>
	</div>
	
	<div class="widget">
	    <label>Template name</label>
	    <input type="text" name="description" value="<?= esc_attr($tfs->getDescription()) ?>" />
	</div>
	<div class="widget">
	    <label>Default template</label>
	    <input type="checkbox" name="default_template" <?= $file == $ftSettings->getDefaultTemplateFile() ? 'checked=checked' : '' ?> />
	</div>
	
	<div class="clear"></div>
    
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
	
	var htmlTab = <?php
	   print json_encode(get_component('fastsite'
	       , 'template/templateFile'
	       , 'snippet'
	       , array(
	           'template' => $template
	       )))
	?>;
	
	var t = tc.addTab('Snippet', htmlTab);
	var sc = new SnippetController(t);

	if (opts.xpath) {
		sc.setXPath( opts.xpath );
	}
	if (opts.snippet_name) {
		sc.setSnippetName( opts.snippet_name );
	}
	if (opts.code) {
		sc.setCode( opts.code );
	}
}

<?php foreach($snippets as $s) : ?>
add_snippet(  <?= json_encode($s) ?> );
<?php endforeach; ?>


function templateFile_Submit() {
	$('#snippet-container .snippet-container').each(function(index, node) {
		$(node).find('select, input, textarea').each(function(index2, node2) {
			var n = $(node2).attr('name');
			n = 'snippets['+index+'][' + n + ']';

			$(node2).attr('name', n);
		});
	});

	return false;
}



</script>


