<?php


use core\controller\BaseController;
use core\exception\FileException;
use core\exception\InvalidStateException;

class editImageController extends BaseController {
    
    
    public function action_index() {
        $f = get_var('f');
        $fullpath = get_data_file_safe('fastsite/fs-media/', $f);
        $basepath = get_data_file_safe('fastsite/fs-media/', '/');
        
        if (!$fullpath) {
            throw new FileException('File not found');
        }
        
        if (strpos($fullpath, $basepath) !== 0) {
            throw new FileException('Invalid path');
        }
        
        $this->f = $f;
        $this->filename = basename($f);
        $this->path = substr($fullpath, strlen($basepath));
        $this->filesize = filesize($fullpath);
        $this->imgUrl = BASE_HREF . 'fs-media'. $this->path;
        
        return $this->render();
    }
    
    
    public function action_save() {
        $f = get_var('f');
        $fullpath = get_data_file_safe('fastsite/fs-media/', $f);
        
        if (!$fullpath) {
            throw new FileException('File not found');
        }
        
        
        if (class_exists('Imagick')) {
            $this->handle_imagick($fullpath, $_REQUEST);
        } else {
            $this->handle_gd($fullpath, $_REQUEST);
        }
        
        redirect('/?m=fastsite&c=media/info&f='.urlencode($f));
    }
    
    public function handle_imagick($path, $opts) {
        $imgsrc = new Imagick();
        $imgsrc->readImage( $path );
        $imgsrc->setimageorientation(imagick::ORIENTATION_UNDEFINED);
        
        $imgsrc_w = $imgsrc->getimagewidth();
        $imgsrc_h = $imgsrc->getimageheight();
        
        
        $img = new Imagick();
        $canvasSize = $imgsrc_w > $imgsrc_h ? $imgsrc_w : $imgsrc_h;
        $img->newImage($canvasSize, $canvasSize, 'white', 'jpg');
        //             $img->setcompressionquality(10);     // doesn't do anything?
        $img->compositeimage($imgsrc, Imagick::COMPOSITE_OVER, $canvasSize/2 - $imgsrc_w/2, $canvasSize/2 - $imgsrc_h/2);
        
        $imgsrc->destroy();
        
        if ($opts['degrees'] != '0' && $opts['degrees'] != '360') {
            $img->rotateimage('#ffffff', $opts['degrees']);
            
            $pos = ($img->getimagewidth()-$canvasSize)/2;
            $pos = (int)$pos;
            $img->cropimage($canvasSize, $canvasSize, 0, 0);
        }
        
        
        $cropx1 = (int)$opts['cropx1'];
        $cropy1 = (int)$opts['cropy1'];
        $cropx2 = (int)$opts['cropx2'];
        $cropy2 = (int)$opts['cropy2'];
        
        $img->cropimage($cropx2-$cropx1, $cropy2-$cropy1, $cropx1, $cropy1);
        
        // resize?
        $imgWidth = (int)$opts['img_width'];
        $imgHeight = (int)$opts['img_height'];
        if ($img->getImageWidth() != $imgWidth || $img->getImageHeight() != $imgHeight) {
            $img->resizeimage($opts['img_width'], $opts['img_height'], Imagick::FILTER_LANCZOS, 1);
        }
        
        
        $img->writeImage( $path );
        
        // destroy image
        $img->destroy();
        
        
    }
    
    
    public function handle_gd($path, $opts) {
        
        $imgsrc = gd_load_image( $path );
        
        if ($imgsrc === null) {
            throw new InvalidStateException('Unable to load image');
        }
        
        
        $imgsrc_w = imagesx($imgsrc);
        $imgsrc_h = imagesy($imgsrc);
        
        
        $canvasSize = $imgsrc_w > $imgsrc_h ? $imgsrc_w : $imgsrc_h;
        $img = imagecreatetruecolor($canvasSize, $canvasSize);
        $cWhite = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $cWhite);
        
        imagecopy($img, $imgsrc, $canvasSize/2 - $imgsrc_w/2, $canvasSize/2 - $imgsrc_h/2, 0, 0, $imgsrc_w, $imgsrc_h);
//         unset($imgsrc);
        
        
        if ($opts['degrees'] != '0' && $opts['degrees'] != '360') {
            $img = imagerotate($img, -$opts['degrees'], $cWhite);
            
            $newW = imagesx($img);
            $newH = imagesy($img);
            
            $img = imagecrop($img, array(
                'x' => ($newW-$canvasSize)/2,
                'y' => ($newH-$canvasSize)/2,
                'width' => $canvasSize,
                'height' => $canvasSize
            ));
        }
//         var_export($opts);exit;
//         header('content-type: image/jpeg');imagejpeg($img, null,   90);exit;
        
        
        $cropx1 = (int)$opts['cropx1'];
        $cropy1 = (int)$opts['cropy1'];
        $cropx2 = (int)$opts['cropx2'];
        $cropy2 = (int)$opts['cropy2'];
        
        $img = imagecrop($img, array(
            'x' => $cropx1,
            'y' => $cropy1,
            'width' => $cropx2-$cropx1,
            'height' => $cropy2-$cropy1
        ));
        
        // resize?
        $imgWidth = (int)$opts['img_width'];
        $imgHeight = (int)$opts['img_height'];
        if ((int)imagesx($img) != $imgWidth || (int)imagesy($img) != $imgHeight) {
            $img2 = imagecreatetruecolor($imgWidth, $imgHeight);
            
            imagecopyresized($img2, $img, 0, 0, 0, 0, $imgWidth, $imgHeight, (int)imagesx($img), (int)imagesy($img));
            $img = $img2;
        }
        
//         header('content-type: image/jpeg');imagejpeg($img, null,   90);exit;
        
        gd_write_image($path, $img);
    }
    
    
    
    
}
