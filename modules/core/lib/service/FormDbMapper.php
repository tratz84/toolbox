<?php


namespace core\service;


use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use core\db\DBObject;
use core\exception\DatabaseException;
use core\forms\HiddenField;
use core\forms\lists\ListResponse;
use core\container\ArrayContainer;

class FormDbMapper {
    
    protected $formClass;
    protected $daoClass;
    protected $dbClass;
    protected $dbClassBaseName;
    
    protected $relationMTON = array();
    protected $relationMTO1 = array();
    
    protected $logRefObject = null;
    protected $logCreatedCode = null;
    protected $logUpdatedCode = null;
    protected $logDeletedCode = null;
    protected $logCreatedText = null;
    protected $logUpdatedText = null;
    protected $logDeletedText = null;
    
    protected $publicFields = array();
    
    public function __construct( $formClass, $daoClass ) {
        $this->formClass = $formClass;
        $this->daoClass = $daoClass;
        
        // fetch DBObject-class
        $daoObject = object_container_create( $daoClass );
        $this->dbClass = $daoObject->getObjectName();
        $this->dbClassBaseName = substr($this->dbClass, strrpos($this->dbClass, '\\')+1);
        
        
        // default to all fields in form
        $objForm = new $this->formClass();
        foreach($objForm->getWidgetsRecursive() as $w) {
            if ($w->getName()) {
                $this->publicFields[] = $w->getName();
            }
        }
        
    }
    
    public function getFormClass() { return $this->formClass; }
    
    public function getForm() { return $this->form; }
    public function setForm($f) { $this->form = $f; }
    
    public function getName() {
        $n = $this->getFormClass();
        if (strpos($n, '\\') !== false)
            $n = substr($n, strrpos($n, '\\')+1);
        $n = substr($n, 0, strlen($n)-4); // 4 = length of string 'Form'
        
        return $n;
    }
    
    public function getDbClass() { return $this->dbClass; }
    
    public function getDaoClass() { return $this->daoClass; }
    public function setDaoClass($class) { $this->daoClass = $class; }
    
    public function getPublicFields() { return $this->publicFields; }
    public function setPublicFields($fields) { $this->publicFields = $fields; }
    public function addPublicField($name) { $this->publicFields[] = $name; }
    
    public function getMTON() { return $this->relationMTON; }
    public function getMTO1() { return $this->relationMTO1; }
    
    
    public function addMTON($daoLink, $daoObject, $name=null, $opts=array()) {
        if ($name == null) {
            $name = lcfirst( substr( $daoObject, strrpos($daoObject, '\\')+1, -3 ) ) . 'List';
        }
        
        $this->relationMTON[] = [
            'daoLink'   => $daoLink,
            'daoObject' => $daoObject,
            'name'      => $name,
            'opts'      => $opts
        ];
    }
    
    public function addMTO1($daoObject, $name, $opts=array()) {
        $this->relationMTO1[] = [
            'daoObject' => $daoObject,
            'name'      => $name,
            'opts'      => $opts
        ];
    }
    
    
    public function setLogRefObject( $str ) { $this->logRefObject = $str; }
    public function getLogRefObject() {
        if ($this->logRefObject)
            return $this->logRefObject;
        else
            return $this->dbClass;
    }
    
    public function setLogCreatedCode( $str ) { $this->logCreatedCode = $str; }
    public function getLogCreatedCode() {
        if ($this->logCreatedCode)
            return $this->logCreatedCode;
        else
            return 'created';
    }
    
    public function setLogUpdatedCode( $str ) { $this->logUpdatedCode = $str; }
    public function getLogUpdatedCode() {
        if ($this->logUpdatedCode)
            return $this->logUpdatedCode;
        else
            return 'updated';
    }
    
    public function setLogDeletedCode( $str ) { $this->logDeletedCode = $str; }
    public function getLogDeletedCode() {
        if ($this->logDeletedCode)
            return $this->logDeletedCode;
        else
            return 'deleted';
    }
    
    public function setLogCreatedText( $str ) { $this->logCreatedText = $str; }
    public function getLogCreatedText() {
        if ($this->logCreatedText)
            return $this->logCreatedText;
        else
            return $this->dbClassBaseName . ' created';
    }
    
    public function setLogUpdatedText( $str ) { $this->logUpdatedText = $str; }
    public function getLogUpdatedText() {
        if ($this->logUpdatedText)
            return $this->logUpdatedText;
        else
            return $this->dbClassBaseName . ' updated';
    }
    
    public function setLogDeletedText( $str ) { $this->logDeletedText = $str; }
    public function getLogDeletedText() {
        if ($this->logDeletedText)
            return $this->logDeletedText;
        else
            return $this->dbClassBaseName . ' deleted';
    }
    
    
    
    
}
