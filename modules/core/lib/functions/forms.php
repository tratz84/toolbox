<?php



use core\forms\ColorPickerField;

function render_checkbox($name, $opts) {
    $html = '';
    
    $html .= '<input type="checkbox" class="checkbox-ui" id="'.esc_attr($name).'" name="'.esc_attr($name).'" ' . (isset($opts['checked'])&&$opts['checked']?'checked=checked':'') . ' />';
    $html .= '<label class="checkbox-ui-placeholder" for="'.esc_attr($name).'">'.(isset($opts['label'])?esc_html($opts['label']):'').'</label>';
    
    return $html;
}

function render_radio($name, $value, $opts) {
    $html = '';
    
    $checked = isset($opts['checked'])&&$opts['checked'];
    $html .= '<input type="radio" class="radio-ui" id="'.esc_attr($name.'-'.$value).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'" ' . ($checked?'checked=checked':'') . ' />';
    $html .= '<label class="radio-ui-placeholder" for="'.esc_attr($name.'-'.$value).'"></label>';
    
    return $html;
}

function render_colorpicker($name, $label, $value) {
    $cp = new ColorPickerField($name);
    $cp->setLabel($label);
    $cp->setValue($value);
    
    return $cp->render();
}



function files_error_to_text( $errNo ) {
    
    if (is_array($errNo) && isset($errNo['error'])) {
        $errNo = $errNo['error'];
    }
    
    if ($errNo == 0) {
        return null;
    }
    
    
    if ($errNo === UPLOAD_ERR_INI_SIZE) {
        return 'The uploaded file exceeds the upload_max_filesize directive';
    }
    if ($errNo == UPLOAD_ERR_FORM_SIZE) {
        return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
    }
    if ($errNo == UPLOAD_ERR_PARTIAL) {
        return 'The uploaded file was only partially uploaded';
    }
    if ($errNo == UPLOAD_ERR_NO_FILE) {
        return 'No file was uploaded.';
    }
    if ($errNo == UPLOAD_ERR_NO_TMP_DIR) {
        return 'Missing a temporary folder';
    }
    if ($errNo == UPLOAD_ERR_CANT_WRITE) {
        return 'Failed to write file to disk';
    }
    if ($errNo == UPLOAD_ERR_EXTENSION) {
        return 'A PHP extension stopped the file upload';
    }
    
    return 'Unknown error';
}

