
<div class="snippet-container">

    <div>
    	<label>X-Path</label>
    	<input type="text" name="snippet_xpath" class="snippet-xpath" />
    </div>
    
    <div>
    	<label>Snippet naam</label>
    	<select name="snippet_name" class="snippet-name">
    		<option value="">Make your choice</option>
    		<?php foreach($snippets as $s) : ?>
    		<option value="<?= esc_attr($s) ?>"><?= esc_html($s) ?></option>
    		<?php endforeach; ?>
    		<option value="" data-new="1">New snippet</option>
    	</select>
    	
    </div>
    
    <div>
    	<label>Snippet code</label>
    	<textarea name="snippet_code" class="snippet-code"></textarea>
    </div>
    
</div>

