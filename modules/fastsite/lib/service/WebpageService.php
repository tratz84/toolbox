<?php

namespace fastsite\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fastsite\form\WebpageForm;
use fastsite\model\Webpage;
use fastsite\model\WebpageDAO;
use fastsite\model\WebpageRevDAO;
use fastsite\model\WebpageRev;

class WebpageService extends ServiceBase {
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    public function readWebpage($webpageId) {
        $wDao = new WebpageDAO();
        $w = $wDao->read($webpageId);
        
        
        $wrDao = new WebpageRevDAO();
        $wr = $wrDao->read($w->getWebpageRevId());
        
        // set fields in the right order
        $r = new Webpage();
        $r->setFields( $wr->getFields() );
        $r->setFields( $w->getFields() );
        $w->setRevision($wr);
        
        return $r;
    }

    public function readWebpageByUrl($url) {
        $wDao = new WebpageDAO();
        
        $w = $wDao->readByUrl($url);
        
        if ($w) {
            $wrDao = new WebpageRevDAO();
            $wr = $wrDao->read($w->getWebpageRevId());
            
            if ($wr) {
                $w->setRevision($wr);
            }
        }
        
        return $w;
    }
    
    public function readWebpageByCode($code) {
        $code = trim($code);
        
        if ($code == '') {
            return null;
        }
        
        $wDao = new WebpageDAO();
        
        return $wDao->readByCode($code);
    }
    
    public function saveWebpage(WebpageForm $webpageForm) {
        $webpageDao = new WebpageDAO();
        $webpageRevDao = new WebpageRevDAO();
        
        $webpageId = $webpageForm->getWidgetValue('webpage_id');
        if ($webpageId) {
            $webpage = $webpageDao->read($webpageId);
        } else {
            $webpage = new Webpage();
        }
        
        $webpageForm->fill($webpage, ['active', 'module', 'webpage_id']);
        $webpage->setCode( trim($webpageForm->getWidgetValue('code')) );
        $webpage->setUrl( trim($webpageForm->getWidgetValue('url')) );
        $webpage->setFastsiteTemplateFile( $webpageForm->getWidgetValue('fastsite_template_file') );
        $webpage->save();
        
        $webpageRev = new WebpageRev();
        $webpageForm->fill($webpageRev, ['webpage_id', 'meta_title', 'meta_description', 'meta_keywords', 'content1', 'content2']);
        $webpageRev->setWebpageId($webpage->getWebpageId());
        $webpageRev->setRev( $webpageRevDao->nextRevNo($webpage->getWebpageId()) );
        $webpageRev->save();
        
        // set last rev
        if ($webpage->getWebpageRevId() != $webpageRev-> getWebpageRevId()) {
            $webpageDao->updateWebpageRev($webpage->getWebpageId(), $webpageRev->getWebpageRevId());
        }
        
        
        return $webpage;
    }
    
    
    public function searchWebpage($start, $limit, $opts = array()) {
        $wDao = new WebpageDAO();
        
        $cursor = $wDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('webpage_id', 'code', 'module', 'url', 'meta_title'));
        
        return $r;
    }
    
    
    public function readAllWebpages() {
        $wDao = new WebpageDAO();
        
        return $wDao->readAll();
    }
    
    
    public function readByUrl($url) {
        $wDao = new WebpageDAO();
        
        $wrDao = new WebpageRevDAO();
        
        // find by url?
        $page = $wDao->readByUrl($url);
        if ($page) {
            $wr = $wrDao->read($page->getWebpageRevId());
            if ($wr)
                $page->setRevision($wr);
            
            return $page;
        }
        
        // remove questionmark & retry
        $p = strpos($url, '?');
        if ($p !== false) {
            $url = substr($url, 0, $p);
            
            $page = $wDao->readByUrl($url);
            
            if ($page) {
                $wr = $wrDao->read($page->getWebpageRevId());
                if ($wr)
                    $page->setRevision($wr);
                
                return $page;
            }
        }
        
        return null;
    }
    
    
}
