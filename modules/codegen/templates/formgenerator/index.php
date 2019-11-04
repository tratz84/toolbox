
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Form generator</h1>
</div>



<?= $form->render() ?>


<div id="tree">
</div>

<script>

$(document).ready(function() {
	
	$('#tree').jstree({

		core: {
			animation: false,
			check_callback: true,
			data: [
				{ 'text': 'blabla ',
					state: { 'opened': true },
					type: 'container',
					'children' :
					[
						{ 'text': 'sub 123', type: 'file' }
					] },
				{
					'text': 'hmz',
					type: 'container'
				}
			]
		},
		'types': {
			'file': {
				icon: 'fa fa-sticky-note-o',
				'valid_children': []
			},
			'container': {
				'icon': 'fa fa-folder-o',
				'valid_children': ['container', 'file']
			}
		},
		plugins: [ 'themes', 'dnd', 'state', 'types', 'html_data' ]
		
	}).bind("loaded.jstree", function (event, data) {
        $(this).jstree("open_all");
    });      

	
	
	
});

</script>
