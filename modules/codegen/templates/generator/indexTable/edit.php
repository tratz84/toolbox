
<style type="text/css">

.codegen-form-index-table-column-list-edit table
, .codegen-form-index-table-column-list-edit thead
, .codegen-form-index-table-column-list-edit tbody
, .codegen-form-index-table-column-list-edit tr
, .codegen-form-index-table-column-list-edit td {
    display: block;
    width: 100%;
}
.codegen-form-index-table-column-list-edit th.th-sortable
, .codegen-form-index-table-column-list-edit td.td-sortable { display: none; }
.codegen-form-index-table-column-list-edit .widget.textarea-field-widget textarea { width: 100%; }

.codegen-form-index-table-column-list-edit tr {
    display: flex;
    flex-direction: column;
}


</style>

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=generator/indexTable') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Edit IndexPage</h1>
</div>



<?= $form->render() ?>



<div class="sample-output"></div>



