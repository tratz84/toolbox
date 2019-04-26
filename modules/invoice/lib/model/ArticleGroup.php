<?php


namespace invoice\model;


class ArticleGroup extends base\ArticleGroupBase {

    public function __construct() {
        parent::__construct();
        
        $this->setActive(true);
    }
    
    public function setParentArticleGroupId($p) {
        if ($p == 0) {
            $this->fields['parent_article_group_id'] = null;
        } else {
            parent::setParentArticleGroupId($p);
        }
    }
    
    public function save() {
        $this->setParentArticleGroupId(0);
        
        return parent::save();
    }
    

}

