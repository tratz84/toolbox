<?php

namespace dataimport\validator;


class DataImportFormValidator {
    
    protected $form = null;
    protected $errors = array();
    
    public function __construct( $form ) {
        $this->form = $form;
    }
    
    public function getErrors() { return $this->errors; }
    
    public function addError( $err ) {
        $this->errors[] = $err;
    }
    
    
}

