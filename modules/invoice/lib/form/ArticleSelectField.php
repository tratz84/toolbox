<?php

namespace invoice\form;

use core\forms\DynamicSelectField;
use invoice\service\ArticleService;


class ArticleSelectField extends DynamicSelectField {
    
    protected $customerDeleted = false;
    
    protected $customerType = null;
    
    public function __construct($name='article_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=invoice&c=article&a=select2';
        if ($label == null) $label = t('Article');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $articleId = null;
        
        // try get
        $func = 'get'.dbCamelCase( $this->getName() );
        if (is_object($obj) && method_exists($obj, $func)) {
            $articleId = $obj->$func();
        }
        
        if (is_array($obj) && isset($obj[ $this->getName() ])) {
            $articleId = $obj[ $this->getName() ];
        }
        
        if ($articleId == null) {
            return;
        }
        
        $aService = object_container_get( ArticleService::class );
        $article = $aService->readArticle( $articleId );
        if ($article) {
            $this->setDefaultText( $article->getArticleName() );
        } else {
            $this->setDefaultText( t('Make your choice') );
        }
    }
    
    
}


