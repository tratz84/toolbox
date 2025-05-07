<?php


namespace webmail\form;


use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\TinymceField;
use core\forms\validator\NotEmptyValidator;
use webmail\service\EmailTemplateService;
use core\forms\CheckboxField;
use core\forms\Select2Field;

class TemplateForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('template_id');
        
        $this->addWidget(new HiddenField('template_id'));
        
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        
        
        $etservice = object_container_get( EmailTemplateService::class );
        $mts = $etservice->listMasterTemplates();
        if (count($mts) > 0) {
            $mtopts = array();
            $mtopts[''] = array('description' => t('Make your choice'));
            foreach($mts as $mt) {
                $mtopts[ $mt->getMasterTemplateId() ] = array('description' => $mt->getName());
            }
            
            $sf_mtid = new Select2Field('master_template_id', null, $mtopts, t('Master template'));
            
            $this->addWidget( $sf_mtid );
        }
        
        
        $this->addWidget(new TextField('template_code', '', 'Code'));
        $this->getWidget('template_code')->setInfoText('Unieke code waarmee bepaald wordt welk template te gebruiken bij het versturen van bijvoorbeeld offerte mails');
        
        $this->addWidget(new TextField('name', '', 'Naam'));
        $this->getWidget('name')->setInfoText('Naam van het template');
        
        $this->addWidget(new TemplateToLineWidget('templateTos'));
        
        $this->addWidget(new TextField('subject', '', 'Onderwerp e-mail'));
        
        $this->addWidget(new TinymceField('content', '', 'Template'));
        
        
        

        $this->addValidator('template_code', new NotEmptyValidator());
        $this->addValidator('template_code', function($form) {
            $code = $form->getWidgetValue('template_code');
            if (trim($code) == '')
                return null;
            
            $ets = ObjectContainer::getInstance()->get(EmailTemplateService::class);
            
            $t = $ets->readByTemplateCode( $code );
            
            if ($t && $t->getTemplateId() != $form->getWidgetValue('template_id')) {
                return 'Reeds in gebruik';
            }
            
            return null;
        });
        $this->addValidator('name', new NotEmptyValidator());
        
        $this->addValidator('templateTos', function($form) {
            $templateTos = $form->getWidget('templateTos');
            
            $objs = $templateTos->getObjects();
            for($x=0; $x < count($objs); $x++) {
                if (validate_email($objs[$x]['to_email']) == false) {
                    return 'Ongeldig mailadres geadresseerde ('.($x+1).')';
                }
            }
            
            return null;
        });
    }
    
    
}

