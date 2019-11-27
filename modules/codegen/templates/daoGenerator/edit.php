
<style type="text/css">

.form-generator label { width: 350px; margin-top: 0px; }

.checkbox-ui-container.widget.core-forms-checkbox-field {
    overflow: auto;
    padding: 2px 0 4px;
}
.checkbox-ui-container.widget.core-forms-checkbox-field:hover {
    background-color: #eee;
}

</style>


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=daoGenerator') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>DAO classes for <?= esc_html($mod) ?></h1>
</div>


<?= $form->render() ?>
