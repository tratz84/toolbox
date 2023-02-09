<?php


namespace core\container;


use core\exception\ObjectNotFoundException;
use core\exception\FileException;

class ObjectHookProxy{
    
    
    
    
    public static function createProxy( $class, $params = array() ) {
        
//         if (strpos($class, '\\') !== 0)
//             $class = '\\'.$class;
        
        
        if ( class_exists($class) == false ) {
            throw new ObjectNotFoundException( 'Class not found' );
        }
        
        $tokens = explode("\\", $class);
        $proxyClass = '';
        foreach($tokens as $t) {
            $proxyClass .= ucfirst($t);
        }
        $proxyClass .= 'Proxy';
        
        $rc = new \ReflectionClass($class);
        
        $p = DATA_DIR . '/default/proxy/' . md5($class).'.'.filemtime($rc->getFileName()).'.php';
        if (is_debug() || file_exists($p) == false) {
            self::createProxyClass($p, $class, $proxyClass);
        }
        
        require_once $p;
        
        $pc = "\\toolbox\\proxy\\".$proxyClass;
        if (method_exists($proxyClass, 'getInstance')) {
            $o = $pc::getInstance();
        }
        else {
            $o = new $pc( ... $params );
        }
        
        return $o;
    }
    
    protected static function createProxyClass( $path, $class, $proxyClassName ) {
        
        if (file_exists(DATA_DIR . '/default/proxy/') == false) {
            if (!mkdir( DATA_DIR . '/default/proxy/', 0755 ))
                throw new FileException( 'Unable to create '.DATA_DIR.'/default/proxy' );
        }
        
        
        $rc = new \ReflectionClass($class);
        
        $methods = $rc->getMethods( \ReflectionMethod::IS_PUBLIC );
        
        
        $phpcode = '<?php'.PHP_EOL;
        $phpcode .= PHP_EOL;
        $phpcode .= 'namespace toolbox\proxy;' . PHP_EOL;
        $phpcode .= PHP_EOL;
        $phpcode .= 'class ' . $proxyClassName.' extends \\'.$class.' implements \\core\\container\\ObjectHookProxyInterface {'.PHP_EOL;
        $phpcode .= PHP_EOL;
        $phpcode .= "\t".'protected $callFuncName;'.PHP_EOL;
        $phpcode .= "\t".'protected $callParams;'.PHP_EOL;
        $phpcode .= PHP_EOL;
        $phpcode .= "\t".'protected $proxyFilters = array();'.PHP_EOL;
        
        
        
        foreach($methods as $m) {
            if ($m->getDeclaringClass()->getName() != $class)
                continue;
            if ($m->getName() == '__construct') continue;
            
            
            $params = '';
            foreach($m->getParameters() as $p) {
                
                $par = '$'.$p->getName();
                if ($p->isPassedByReference())
                    $par = '&'.$par;
                
                if ($p->isDefaultValueAvailable()) {
                    if (is_string($p->getDefaultValue()) || is_numeric($p->getDefaultValue())) {
                        $par  = $par . ' = ' . $p->getDefaultValue();
                    }
                    else {
                        $par  = $par . ' = ' . var_export($p->getDefaultValue(), true);
                    }
                }
                
                if ($params == '') {
                    $params = $par;
                } else {
                    $params .= ', '.$par;
                }
            }
            
            
            $phpcode .= PHP_EOL;
            $phpcode .= "\tpublic function ".$m->getName()."({$params}) {" . PHP_EOL;
            
            $phpcode .= "\t\t\$this->callFuncName = ".var_export($m->getName(), true).';' . PHP_EOL;
            $phpcode .= "\t\t\$this->callParams = func_get_args();" . PHP_EOL;
            
            $phpcode .= PHP_EOL;
            
            $phpcode .= "\t\t\$r = \$this->proxy_executeFunction();" . PHP_EOL;
            
            $phpcode .= "\t\treturn \$r;" . PHP_EOL;
            
            $phpcode .= "\t}" . PHP_EOL;
            
            
//             print $m->getName() . ' - ' . $m->getDeclaringClass()->getName() . "\n";
        }
        
        $phpcode .= "\tpublic function proxy_addFilter( \$f ) {" . PHP_EOL;
        $phpcode .= "\t\t\$this->proxyFilters[] = \$f;" . PHP_EOL;
        $phpcode .= "\t}" . PHP_EOL;
        
        $phpcode .= "\tpublic function proxy_executeFunction() {" . PHP_EOL;
        
//         $phpcode .= "var_export(get_class(\$this));";
//         $phpcode .= "var_export(\$this->callFuncName);";
//         $phpcode .= "var_export(\$this->callParams);";
        $phpcode .= "\t\t\$ohc = new \\core\\container\\ObjectHookCall( \$this, \$this->callFuncName, \$this->callParams );".PHP_EOL;
        $phpcode .= "\t\t\$ohc->setFilters( \$this->proxyFilters );".PHP_EOL;
        $phpcode .= "\t\treturn \$ohc->next();".PHP_EOL;
        
        $phpcode .= "\t}" . PHP_EOL;
        
        
        
        $phpcode .= '}'.PHP_EOL;
        
//         print $phpcode;exit;
        
        
        file_put_contents( $path, $phpcode );
        
        require_once( $path );
    }
    
    
}


