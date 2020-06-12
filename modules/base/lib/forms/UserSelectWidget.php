<?php

namespace base\forms;

use core\forms\DynamicSelectField;

class UserSelectWidget extends DynamicSelectField {
    
    
    public function __construct($name='user_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = 'Maak uw keuze';
        if ($endpoint == null) $endpoint = '/?m=base&c=user&a=select2';
        if ($label == null) $label = t('User');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
    }
    
}


