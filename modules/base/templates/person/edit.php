
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=person') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add person') ?>: <span id="h1-person-name"></span></h1>
    <?php else : ?>
    <h1 id="h1-person-name"><?= esc_html(format_personname($person)) ?></h1>
    <?php endif; ?>
</div>

<?= $actionContainer->render() ?>

<?php
    $tabContainer = generate_tabs('base', 'person-edit-footer', $form);
    $tabContainer->addTab(t('Personal data'), $form->render(), 0);
    print $tabContainer->render();
?>


<br/>

<script>

$(document).ready(function() {
	link_input2text('[name=firstname], [name=insert_lastname], [name=lastname]', function() {
		var f = $('[name=firstname]').val();
		var il = $('[name=insert_lastname]').val();
		var l = $('[name=lastname]').val();

		// lastname + insert_lastname
		var t = '';
		t = l + ' ' + il;
		t = $.trim(t);

		// lastname set & there is a firstname? => add a comma
		f = $.trim(f);
		if (f != '' && t != '') {
			 t = t + ', ';
		}

		// add lastname
		t += f;

		// done
		$('#h1-person-name').text( t );
	});
});

</script>
