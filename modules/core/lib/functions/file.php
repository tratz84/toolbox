<?php



function readfile_with_headers( $path, $filename=null ) {
    if ($filename == null) {
        $filename = basename( $path );
    }
    
    $mimetype = false;
    $mimetype = toolbox_mime_content_type($path);
    if ($mimetype) {
        header('Content-Type: '.$mimetype);
    } else {
        header('Content-Type: application/octet-stream');
    }
    
    // set cache headers, content won't change..
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24))); // 24 hours
    header("Pragma: cache");
    header("Cache-Control: max-age=3600");
    header('Content-Disposition: inline; filename="'.$filename.'"');
    
    readfile( $path );
}



