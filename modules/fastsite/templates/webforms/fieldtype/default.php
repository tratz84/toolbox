
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
				<option value="<?= esc_attr($v['class']) ?>"><?= esc_html($v['label']) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<div class="widget">
		<label>Veldnaam</label>
		<input type="text" name="wf[x][fieldname]" />
	</div>
	
	<div class="widget">
		<label>Placeholder</label>
		<input type="text" name="wf[x][placeholder]" />
	</div>
	
	
</div>

