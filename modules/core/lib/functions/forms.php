<?php



use core\forms\ColorPickerField;

function render_checkbox($name, $opts) {
    $html = '';
    
    $html .= '<input type="checkbox" class="checkbox-ui" id="'.esc_attr($name).'" name="'.esc_attr($name).'" ' . (isset($opts['checked'])&&$opts['checked']?'checked=checked':'') . ' />';
    $html .= '<label class="checkbox-ui-placeholder" for="'.esc_attr($name).'"></label>';
    
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

