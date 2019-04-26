<?php


namespace invoice\model;


class Article extends base\ArticleBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
        $this->setDeleted(false);
    }

    public static function getArticleTypes() {
        return array(
            'normal',
            'deposit'
        );
    }
    
    
}

