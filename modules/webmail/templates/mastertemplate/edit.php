


<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=mastertemplate') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Template toevoegen</h1>
    <?php else : ?>
    <h1>Template bewerken</h1>
    <?php endif; ?>
</div>



<?= $form->render() ?>



<?php if ($hasTemplate) : ?>
<br/>
<?php if (strpos($mt->getContent(), '[[content]]') === false) : ?>
	<div class="error">
		<?= t('Warning: template does NOT contain the [[content]]-placeholder!') ?>
	</div>
<?php endif; ?>

<iframe src="<?= appUrl('/?m=webmail&c=mastertemplate&a=view_tpl&id='.$mt->getMasterTemplateId()) ?>" style="width: 100%; max-width: 800px; height: 700px; border: 1px solid #ccc;"></iframe>

<?php endif; ?>

