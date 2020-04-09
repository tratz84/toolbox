<?php


use core\ObjectContainer;
use core\template\HtmlScriptLoader;
use base\service\SettingsService;

// register tinymce script. Loaded only when needed, because loading sometimes hanged because of it.. :/
$oc = ObjectContainer::getInstance();
$hsl = $oc->get(HtmlScriptLoader::class);

hook_register_css('tinymce', '/lib/tinymce/skins/lightgray/skin.min.css', ['position' => 'bottom']);
hook_register_javascript('tinymce', '/lib/tinymce/tinymce.min.js',        ['position' => 'bottom']);
hook_register_javascript('tinymce', '/lib/tinymce/jquery.tinymce.min.js', ['position' => 'bottom']);


// register jquery-colorpicker
hook_register_css('jquery-colorpicker', '/lib/colorpicker/css/colorpicker.css',      ['position' => 'top']);
hook_register_javascript('jquery-colorpicker', '/lib/colorpicker/js/colorpicker.js', ['position' => 'top']);

hook_register_javascript('iban', '/js/iban.js', ['position' => 'top']);



// overwrite CSS colors
$settingsService = object_container_get( SettingsService::class );
$settings = $settingsService->settingsAsMap();
$master_base_color = valid_rgbhex($settings['master_base_color']) ? $settings['master_base_color'] : '#f00';
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


