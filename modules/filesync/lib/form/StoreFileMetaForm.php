<?php

namespace filesync\form;



use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DatePickerField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\TextField;
use core\forms\TextareaField;
use customer\forms\CustomerSelectWidget;
use core\forms\InternalField;

class StoreFileMetaForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('store_file_id');
        
        $this->addWidget(new InternalField('public_secret'));
        $this->addWidget(new HiddenField('store_id'));
        $this->addWidget(new HiddenField('store_file_id'));
        $this->addWidget(new HtmlField('path', '', 'Pad'));
        $this->addWidget(new DatePickerField('document_date', '', 'Document datum'));
        $this->addWidget(new CustomerSelectWidget());
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        $this->addWidget(new CheckboxField('public', '', 'Public'));
        $this->getWidget('public')->setInfoText('Create download link for file?');
    }
    
    
    public function render() {
        
        if ($this->getWidgetValue('public')) {
            if (!$this->getWidget('public_url')) {
                $this->addWidget(new HtmlField('public_url', '', 'Public url'));
                $this->getWidget('public_url')->setEscapeValue( false );
            }
            
            $sfid = $this->getWidgetValue('store_file_id');
            $s = $this->getWidgetValue('public_secret');
            $u = BASE_URL . appUrl('/?m=filesync&c=public/storefile&a=download&sfid='.$sfid.'&ps='.$s);
            
            $this->getWidget('public_url')->setValue( '<a href="'.esc_attr($u).'" target="_blank">'.esc_html($u).'</a>' );
        }
        
        return parent::render();
    }
    
}

