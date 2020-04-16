
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="save_Click();" class="fa fa-save"></a>
	</div>

	<h1>Model generator</h1>
</div>

<form method="post" id="frm">
	Table name: <input type="text" name="tbl" value="<?= esc_attr($tbl) ?>" placeholder="&lt;schemaname&gt;.&lt;tablename&gt;" style="width: 300px;" />
</form>

<?php if (is_get()) : ?>
<div style="font-style: italic; margin-top: 10px;">This function only generates text to paste in a tablemodel.php-file</div>
<?php endif; ?>

<?php if (is_post() && isset($model_code)) : ?>
<pre><?= esc_html($model_code) ?>


<?= esc_html($create_table) ?></pre>
<?php endif; ?>



<script>

function save_Click() {
	$('#frm').submit();
}

$('[name=tbl]').focus();

</script>

