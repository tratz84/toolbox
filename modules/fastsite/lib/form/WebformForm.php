<?php


namespace fastsite\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\RadioField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\TinymceField;
use core\forms\WidgetContainer;
use core\forms\validator\EmailValidator;
use core\forms\validator\IbanValidator;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\NotFirstOptionValidator;
use fastsite\model\Webform;

class WebformForm extends BaseForm {
    
    protected $webformFields = array();
    
    protected $webformFieldTypes = array();
    protected $webformValidators = array();
    
    
    public function __construct() {
        parent::__construct();
        
        $this->webformFieldTypes[] = array( 'class' => TextField::class,     'label' => 'Tekstregel' );
        $this->webformFieldTypes[] = array( 'class' => TextareaField::class, 'label' => 'Tekstveld (multi-line)' );
        $this->webformFieldTypes[] = array( 'class' => EmailField::class,    'label' => 'E-mail' );
        $this->webformFieldTypes[] = array( 'class' => SelectField::class,   'label' => 'Select-field' );
        $this->webformFieldTypes[] = array( 'class' => RadioField::class,    'label' => 'Radio buttons' );
        
        $this->webformValidators[] = array( 'class' => NotEmptyValidator::class,       'label' => 'Waarde verplicht' );
        $this->webformValidators[] = array( 'class' => NotFirstOptionValidator::class, 'label' => 'Eerste waarde niet toegestaan (radio/select veld)' );
        $this->webformValidators[] = array( 'class' => EmailValidator::class,          'label' => 'E-mail validation' );
        $this->webformValidators[] = array( 'class' => IbanValidator::class,           'label' => 'IBAN validation' );
        
        
        $this->addWidget(new HiddenField('webform_id'));
        $this->addWidget(new CheckboxField('active', '', 'Actief'));
        $this->addWidget(new TextField('webform_name', '', 'Formulier naam'));
        $this->addWidget(new TextField('webform_code', '', 'Formulier code'));
        
        $this->addWidget(new TinymceField('confirmation_message', '', 'Bevestigingsbericht'));
        $this->getWidget('confirmation_message')->setInfoText('Getoond bericht na versturen formulier');
        
        $this->addWidget(new HtmlField('form-fields', '', 'Input fields'));
        $this->addWidget(new WidgetContainer('webform-fields'));
    }
    
    public function getWebformFieldTypes() { return $this->webformFieldTypes; }
    public function getWebformValidators() { return $this->webformValidators; }
    
    public function getWebformFields() { return $this->webformFields; }
    
    
    public function bind($obj) {
        $r = parent::bind($obj);
        
        if (is_array($obj) && isset($obj['wf'])) {
            $this->webformFields = $obj['wf'];
        }
        if (is_a($obj, Webform::class)) {
            $wfs = $obj->getWebformFields();
            foreach($wfs as $wf) {
                $this->webformFields[] = $wf->getFields();
            }
        }
        
        
        return $r;
    }
}
