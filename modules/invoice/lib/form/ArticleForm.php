<?php

namespace invoice\form;

use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EuroField;
use core\forms\HiddenField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\NotEmptyValidator;
use invoice\service\InvoiceService;
use invoice\service\ArticleService;
use invoice\model\Article;

class ArticleForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget( new HiddenField('article_id', '', 'Id') );
//         $this->addWidget( new HiddenField('article_type', 'normal', 'Artikel type') );      // ie normal, rental, ...
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        
        $mapArticleTypes = array();
        foreach(Article::getArticleTypes() as $at) {
            $mapArticleTypes[$at] = t('articleType.'.$at);
        }
        $this->addWidget( new SelectField('article_type', '', $mapArticleTypes, 'Artikelsoort') );
        $this->getWidget('article_type')->setInfoText('Soort artikel. Standaard staat deze op: "Normaal". Mocht u gebruik maken van de verhuur-module en borgsom, kan er een artikelsoort "Borg" worden toegevoegd.');
        
        $this->addWidget( new TextField('article_name', '', 'Naam') );
        $this->addWidget( new EuroField('price', '', 'Prijs excl. btw') );
        $this->addVat();
        $this->addWidget( new CheckboxField('rentable', '', 'Verhuurbaar'));
        $this->addWidget( new CheckboxField('simultaneously_rentable', '', 'Overboekbaar'));
        
        $this->addWidget( new TextareaField('long_description1', '', 'Omschrijving 1') );
        $this->addWidget( new TextareaField('long_description2', '', 'Omschrijving 2') );
        
        
        $this->addValidator('article_name', new NotEmptyValidator());
        
        // check dubbele naam
        $this->addValidator('article_name', function($form) {
            $articleService = ObjectContainer::getInstance()->get(ArticleService::class);
            
            $article_name = trim($form->getWidget('article_name')->getValue());
            $listResponse = $articleService->searchArticles(0, 20, array('article_type' => 'normal', 'article_name_exact' => $article_name));
            
            foreach($listResponse->getObjects() as $o) {
                if ($o['article_id'] != $form->getWidget('article_id')->getValue()) {
                    return 'Er bestaat reeds een artikel met deze naam';
                }
            }
            
            return null;
        });
        
        
        $this->addValidator('vat_id', new NotEmptyValidator());
    }
    
    
    protected function addVat() {
        $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
        
        $vats = $invoiceService->readActiveVatTarifs();
        $map = array();
        foreach($vats as $v) {
            $map[$v->getVatId()] = $v->getDescription();
        }
        
        $this->addWidget( new SelectField('vat_id', '', $map, 'Btw') );
    }
}

