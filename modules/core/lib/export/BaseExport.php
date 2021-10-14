<?php



namespace core\export;


class BaseExport {
    
    protected $fields;
    
    protected $callbackColumnValue = null;
    protected $rfCallbackColumnValue = null;
    
    public function __construct($fields=array()) {
        
        $this->fields = $fields;
    }
    
    public function addField( $name, $label=null, $type='text' ) {
        if ($label === null) $label = $name;
        
        $this->fields[] = array(
            'name'    => $name
            , 'label' => $label
            , 'type'  => $type
        );
    }
    
    
    public function setCallbackColumnValue( $callback ) {
        $this->callbackColumnValue = $callback;
    }
    
    public function callCallbackColumnValue( $fieldName, $val, $opts=array() ) {
        if (!$this->callbackColumnValue) {
            return $val;
        }
        
        if (!$this->rfCallbackColumnValue) {
            $callbackColumnValue = $this->callbackColumnValue;
            $this->rfCallbackColumnValue = new \ReflectionFunction( $callbackColumnValue );
        }
        
        $callback = $this->callbackColumnValue;
        
        if ($this->rfCallbackColumnValue->getNumberOfParameters() == 3) {
            $val = $callback( $fieldName, $val, $opts );
        }
        else {
            $val = $callback( $fieldName, $val );
        }
        
        return $val;
    }
    
    
    public function getHeaders() {
        $headers = array();
        
        foreach($this->fields as $f) {
            $headers[] = $f['label'];
        }
        
        return $headers;
    }
    
    
    
}

