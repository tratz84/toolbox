<?php


// register tinymce script. Loaded only when needed, because loading sometimes hanged because of it.. :/
use base\service\SettingsService;
use core\filter\ModuleEnablerFilter;

hook_register_css('tinymce', '/lib/tinymce/skins/lightgray/skin.min.css', ['position' => 'bottom']);
hook_register_javascript('tinymce', '/lib/tinymce/tinymce.min.js',        ['position' => 'bottom']);
hook_register_javascript('tinymce', '/lib/tinymce/jquery.tinymce.min.js', ['position' => 'bottom']);


// register jquery-colorpicker
hook_register_css('jquery-colorpicker', '/lib/colorpicker/css/colorpicker.css',      ['position' => 'top']);
hook_register_javascript('jquery-colorpicker', '/lib/colorpicker/js/colorpicker.js', ['position' => 'top']);

hook_register_javascript('iban', '/js/iban.js', ['position' => 'top']);



hook_eventbus_subscribe('core', 'filter-executed', function( $filter ) {
    
    // add css after all modules are enabled
    if (is_a($filter, ModuleEnablerFilter::class)) {
        // overwrite CSS colors
        $master_base_color = ctx()->getSetting('master_base_color');
        $master_base_color = valid_rgbhex($master_base_color) ? $master_base_color : '#f00';
        $rgbMasterColor = hex2rgb($master_base_color);
        
        $coreCssText = <<<CSS
header .notifications-bar {
    background-color: rgba({$rgbMasterColor[0]}, {$rgbMasterColor[1]}, {$rgbMasterColor[2]}, 1);
}

.dashboard-widgets .widget-item .widget-title {
    background-color: rgba({$rgbMasterColor[0]}, {$rgbMasterColor[1]}, {$rgbMasterColor[2]}, 0.7) !important;
}

.context-background {
    background-color: rgba({$rgbMasterColor[0]}, {$rgbMasterColor[1]}, {$rgbMasterColor[2]}, 1) !important;
}

CSS;
        hook_add_inline_css($coreCssText);
    }
});


