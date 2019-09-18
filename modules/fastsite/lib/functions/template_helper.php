<?php



function tpl_load_snippet($templateName, $snippetName) {
    
    $templateName = basename( $templateName );
    $snippetName = basename( $snippetName );
    
    
    $f = get_data_file_safe('fastsite/templates', $templateName.'/fastsite/snippet-'.$snippetName.'.php');
    
    if (!$f) {
        return false;
    }
    
    return file_get_contents( $f );
}

