<?php


namespace core\container;


use core\exception\ObjectNotFoundException;
use core\exception\FileException;

class ObjectHookProxyExtender {
    
    
    
    
    public static function createProxy( $class ) {
        
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
            $o = new $pc();
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
        $phpcode .= 'class ' . $proxyClassName.' extends \\'.$class.' {'.PHP_EOL;
        
        
        
        foreach($methods as $m) {
            if ($m->getDeclaringClass()->getName() != $class)
                continue;
            if ($m->getName() == '__construct') continue;
            
            
            $params = '';
            $paramsFunc = '';
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
                    $paramsFunc = '$'.$p->getName();
                } else {
                    $params .= ', '.$par;
                    $paramsFunc .= ', $'.$p->getName();
                }
            }
            
            
            $phpcode .= PHP_EOL;
            $phpcode .= "\tpublic function ".$m->getName()."({$params}) {" . PHP_EOL;
            
            
            $phpcode .= "\t\t\$ohc = new \core\container\ObjectHookCall(\$this, ".var_export($m->getName(), true).", func_get_args());".PHP_EOL;
            $phpcode .= "\t\t\$eb = object_container_get(\\core\\event\\EventBus::class);" . PHP_EOL;
            
            $eventNamePre = 'pre-call-'.$class.'::'.$m->getName();
            
            $phpcode .= "\t\t\$eb->publishEvent(\$ohc, 'core', ".var_export($eventNamePre,true).");".PHP_EOL;
            
            $phpcode .= "\t\t\$eb->publishEvent(\$ohc, 'core', 'pre-object-hook-call');".PHP_EOL;
            
            $phpcode .= PHP_EOL;
            
            $phpcode .= "\t\t\$r = parent::".$m->getName().'( '.$paramsFunc.' );' . PHP_EOL;
            
            $phpcode .= PHP_EOL;
            
            $phpcode .= "\t\t\$ohc->setReturnValue(\$r);" . PHP_EOL;
            
            $eventNamePost = 'post-call-'.$class.'::'.$m->getName();
            $phpcode .= "\t\t\$eb->publishEvent(\$ohc, 'core', ".var_export($eventNamePost, true).");".PHP_EOL;
            
            $phpcode .= "\t\t\$eb->publishEvent(\$ohc, 'core', 'post-object-hook-call');".PHP_EOL;
            
            $phpcode .= PHP_EOL;
            
            $phpcode .= "\t\treturn \$r;" . PHP_EOL;
            
            $phpcode .= "\t}" . PHP_EOL;
            
//             print $m->getName() . ' - ' . $m->getDeclaringClass()->getName() . "\n";
        }
        
        
        
        $phpcode .= '}'.PHP_EOL;
        
//         print $phpcode;exit;
        
        
        file_put_contents( $path, $phpcode );
        
        require_once( $path );
    }
    
    
}


