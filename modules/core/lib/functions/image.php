<?php



use core\exception\InvalidArgumentException;

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


function gd_image_resize( $img, $newWidth, $newHeight=null) {
    if (intval($newWidth) <= 0 && intval($newHeight) <= 0) {
        throw new InvalidArgumentException('Invalid width & height given');
    }
    
    $originalWidth = imagesx( $img );
    $originalHeight = imagesy( $img );
    
    if (intval($newWidth) <= 0) {
        $newWidth = $originalWidth * $newHeight / $originalHeight; 
    }
    if (intval($newHeight) <= 0) {
        $newHeight = $originalHeight * $newWidth / $originalWidth;
    }
    
    $newimg = imagecreatetruecolor( $newWidth, $newHeight );
    imagecopyresized( $newimg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight );
    
    return $newimg;
}



function imagick_image_resize( $im, $newWidth, $newHeight=null ) {
    if (intval($newWidth) <= 0 && intval($newHeight) <= 0) {
        throw new InvalidArgumentException('Invalid width & height given');
    }
    
    $originalWidth = $im->getImageWidth();
    $originalHeight = $im->getImageHeight();
    
    if (intval($newWidth) <= 0) {
        $newWidth = $originalWidth * $newHeight / $originalHeight;
    }
    if (intval($newHeight) <= 0) {
        $newHeight = $originalHeight * $newWidth / $originalWidth;
    }
    
    
    $im->resizeImage( $newWidth, $newHeight,imagick::FILTER_HAMMING, 1);
    
    return $im;
}




