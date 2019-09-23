
<div class="webform-field">

	<div class="toolbox">
		<a class="fa fa-arrows-v move-handle" href="javascript:void(0);"></a>
		<a class="fa fa-remove" href="javascript:void(0);" onclick="$(this).closest('.webform-field').remove();"></a>
	</div>
	
	<div class="widget">
		<label>Veldtype</label>
		
		<input type="hidden" name="wf[x][class]" value="<?= esc_attr($class) ?>" />
		<?= esc_html($fieldtype) ?>
	</div>
	
	<div class="widget">
		<label>Validator</label>
		<select name="wf[x][validator]">
			<option value="">Maak uw keuze</option>
			<?php foreach($validators as $v) : ?>
				<option value="<?= esc_attr($v['class']) ?>" <?= $selected_validator == $v['class'] ? 'selected=selected':'' ?>><?= esc_html($v['label']) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<div class="widget">
		<label>Veldnaam</label>
		<input type="text" name="wf[x][fieldname]" value="<?= esc_attr($fieldname) ?>" />
	</div>
	
	<div class="widget-options">
		<?php if ($inputoptions) foreach($inputoptions as $io) : ?>
		<div class="option-container">
			<span class="handler option-move-handler fa fa-arrows-v ui-sortable-handle"></span>
			<span class="key"><input type="text" name="wf[x][inputoptions][y][key]" placeholder="Key" value="<?= @esc_attr($io->key) ?>" /></span>
			<span class="val"><input type="text" name="wf[x][inputoptions][y][value]" placeholder="Value" value="<?= @esc_attr($io->value) ?>" /></span>
			<a class="fa fa-remove" href="javascript:void(0);" onclick="remove_keyval_option(this);"></a>
		</div>
		<?php endforeach; ?>
	</div>
	
	<a href="javascript:void(0);" onclick="add_keyval_option(this);"><span class="fa fa-plus"></span> Optie toevoegen</a>
	
</div>

