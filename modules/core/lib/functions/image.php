<?php



function gd_load_image($path) {
    
    $ext = file_extension($path);
    
    if ($ext == 'jpg' || $ext == 'jpeg') {
        return imagecreatefromjpeg($path);
    }
    if ($ext == 'png') {
        return imagecreatefrompng($path);
    }
    if ($ext == 'bmp') {
        return imagecreatefromwbmp($path);
    }
    if ($ext == 'gif') {
        return imagecreatefromgif($path);
    }
    
    
    return null;
}

function gd_write_image($path, $img, $opts=array()) {
    
    $ext = file_extension($path);
    
    $q = isset($opts['quality']) ? $opts['quality'] : 85;
    
    if ($ext == 'jpg' || $ext == 'jpeg') {
        return imagejpeg($img, $path, $q);
    }
    if ($ext == 'png') {
        return imagepng($img, $path, $q);
    }
    if ($ext == 'bmp') {
        return image2wbmp($img, $path, null);
    }
    if ($ext == 'gif') {
        return imagegif($img, $path);
    }
    
    return false;
}

function gd_image_supported($filename) {
    $ext = file_extension($filename);
    
    if (in_array($ext, array('jpg', 'jpeg', 'png', 'bmp', 'gif')) == true) {
        return true;
    } else {
        return false;
    }
}



