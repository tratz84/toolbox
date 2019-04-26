<?php

namespace invoice\service;

use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use core\container\ObjectHookable;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use invoice\form\ArticleGroupForm;
use invoice\model\Article;
use invoice\model\ArticleArticleGroupDAO;
use invoice\model\ArticleDAO;
use invoice\model\ArticleGroup;
use invoice\model\ArticleGroupDAO;
use invoice\form\ArticleForm;

class ArticleService extends ServiceBase implements ObjectHookable {
    
    
    public function searchArticles($start=0, $limit=25, $opts=array()) {
        $aDao = new ArticleDAO();
        
        $opts['article_type'] = Article::getArticleTypes();
        $cursor = $aDao->searchCursor($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('article_id', 'article_name', 'active', 'price', 'vat_id', 'vat_percentage', 'vat_description'));
        
        $objs = $r->getObjects();
        for($x=0; $x < count($objs); $x++) {
            $p = $objs[$x]['price'];
            $vatP = $objs[$x]['vat_percentage'];
            $vatAmount = myround(($p * $vatP) / 100, 2);
            $objs[$x]['vat_price'] = $vatAmount;
            
            $objs[$x]['price_incl_vat'] = myround($p + $vatAmount, 2);
        }
        
        $r->setObjects($objs);
        
        
        return $r;
    }
    
    public function readArticle($id) {
        $aDao = new ArticleDAO();
        
        return $aDao->read($id);
    }
    
    public function getArticleName($id) {
        $a = $this->readArticle($id);
        
        if ($a) {
            return $a->getArticleName();
        } else {
            return null;
        }
    }
    
    public function saveArticle($articleForm) {
        $articleId = $articleForm->getWidgetValue('article_id');
        
        $oldForm = null;
        if ($articleId) {
            $a = $this->readArticle( $articleId );
            $oldForm = ArticleForm::createAndBind($a);
        }
        
        
        $fieldsArticle = array('article_id', 'article_type', 'article_name', 'long_description1', 'long_description2', 'price', 'rentable', 'simultaneously_rentable', 'active', 'vat_id');
        $this->saveForm($articleForm, Article::class, $fieldsArticle);
        
        // log activity
        if (!$articleId) {
            $fch = FormChangesHtml::formNew($articleForm);
            ActivityUtil::logActivity(null, null, 'article__article', $articleForm->getWidgetValue('article_id'), 'article-created', 'Artikel aangemaakt '.$articleForm->getWidgetValue('article_name'), $fch->getHtml());
        } else {
            $fch = FormChangesHtml::formChanged($oldForm, $articleForm);
            ActivityUtil::logActivity(null, null, 'article__article', $articleForm->getWidgetValue('article_id'), 'article-edited', 'Artikel bewerkt '.$articleForm->getWidgetValue('article_name'), $fch->getHtml());
        }
        
        return $articleForm->getWidgetValue('article_id');
    }
    
    public function deleteArticle($articleId) {
        // fetch for logging
        $a = $this->readArticle( $articleId );
        $f = ArticleForm::createAndBind($a);
        $fch = FormChangesHtml::formDeleted($f);
        
        $aDao = new ArticleDAO();
        $r = $aDao->delete($articleId);
        
        // log
        ActivityUtil::logActivity(null, null, 'article__article', $a->getArticleId(), 'article-deleted', 'Artikel verwijderd '.$a->getArticleName(), $fch->getHtml());
        
        hook_eventbus_publish($a, 'invoice', 'article-delete');
        
        return $r;
    }
    
    
    
    public function searchArticleGroup($start=0, $limit=1000, $opts=array()) {
        $agDao = new \invoice\model\ArticleGroupDAO();
        
        $cursor = $agDao->cursorAll();
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('article_group_id', 'group_name', 'long_description1', 'long_description1', 'active'));
        
        
        return $r;
    }
    
    public function readArticleGroup($id) {
        $agDao = new ArticleGroupDAO();
        
        return $agDao->read($id);
    }
    
    public function saveArticleGroup($f) {
        $articleGroupId = $f->getWidgetValue('article_group_id');
        
        $oldForm = null;
        if ($articleGroupId) {
            $ag = $this->readArticleGroup( $articleGroupId );
            $oldForm = ArticleGroupForm::createAndBind($ag);
        }
        
        $fields = array('group_name', 'long_description1', 'long_description2', 'active');
        
        $this->saveForm($f, ArticleGroup::class, $fields);
        
        // log activity
        if (!$articleGroupId) {
            $fch = FormChangesHtml::formNew($f);
            ActivityUtil::logActivity(null, null, 'article__article_group', $f->getWidgetValue('article_group_id'), 'article-group-created', 'Artikelgroep aangemaakt '.$f->getWidgetValue('group_name'), $fch->getHtml());
        } else {
            $fch = FormChangesHtml::formChanged($oldForm, $f);
            ActivityUtil::logActivity(null, null, 'article__article_group', $f->getWidgetValue('article_group_id'), 'article-group-edited', 'Artikelgroep bewerkt '.$f->getWidgetValue('group_name'), $fch->getHtml());
        }
        
        return $f->getWidgetValue('article_group_id');
    }
    
    public function deleteArticleGroup($articleGroupId) {
        // fetch for logging
        $ag = $this->readArticleGroup( $articleGroupId );
        $f = ArticleGroupForm::createAndBind($ag);
        $fch = FormChangesHtml::formDeleted($f);
        
        // delete
        $aagDao = new ArticleArticleGroupDAO();
        $aagDao->deleteByArticleGroup($articleGroupId);
        
        $agDao = new ArticleGroupDAO();
        $agDao->deleteByArticleGroup($articleGroupId);
        
        // log
        ActivityUtil::logActivity(null, null, 'article__article_group', $ag->getArticleGroupId(), 'article-group-deleted', 'Artikelgroep verwijderd '.$ag->getGroupName(), $fch->getHtml());
    }
    
    
}


