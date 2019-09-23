
<div class="webform-field">

	<div class="toolbox">
		<a class="fa fa-remove" href="javascript:void(0);" onclick="$(this).closest('.webform-field').remove();"></a>
	</div>
	
	<div class="widget">
		<label>Veldtype</label>
		
		<?= esc_html($fieldtype) ?>
	</div>
	
	<div class="widget">
		<label>Validator</label>
		<select name="">
			<option value="">Maak uw keuze</option>
			<?php foreach($validators as $v) : ?>
				<option value="<?= esc_attr($v['class']) ?>"><?= esc_html($v['label']) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<div class="widget">
		<label>Veldnaam</label>
		<input type="text" name="" />
	</div>
	
	<div class="widget-options"></div>
	
	<a href="javascript:void(0);" onclick="add_keyval_option(this);"><span class="fa fa-plus"></span> Optie toevoegen</a>
	
</div>

