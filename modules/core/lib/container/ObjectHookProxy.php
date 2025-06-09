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
        else {
            require_once $p;
        }
        
        
        $pc = "\\toolbox\\proxy\\".$proxyClass;
        
        return $pc;
    }
    
    protected static function createProxyClass( $path, $class, $proxyClassName ) {
        if (file_exists(DATA_DIR . '/default/proxy/') == false) {
            if (!mkdir( DATA_DIR . '/default/proxy/', 0755, true ))
                throw new FileException( 'Unable to create '.DATA_DIR.'/default/proxy' );
        }
        
        
        $rc = new \ReflectionClass($class);
        
        $methods = $rc->getMethods( \ReflectionMethod::IS_PUBLIC );
        
        
        $phpcode = '';
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
                    if ( is_numeric($p->getDefaultValue()) ) {
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
//             $phpcode .= "\t\t\$this->callParams = func_get_args();" . PHP_EOL;

            // func_get_args() doesn't work with pass-by-ref params. Set params one-by-one
            $phpcode .= "\t\t\$this->callParams = array();" . PHP_EOL;
            foreach($m->getParameters() as $p) {
                $paramName = "\$".$p->getName();
                if ($p->isPassedByReference())
                    $paramName = "&".$paramName;
                $phpcode .= "\t\t\$this->callParams[] = {$paramName};" . PHP_EOL;
            }
            
            $phpcode .= PHP_EOL;
            
            $phpcode .= "\t\t\$r = \$this->proxy_executeFunction();" . PHP_EOL;
            
            $phpcode .= "\t\treturn \$r;" . PHP_EOL;
            
            $phpcode .= "\t}" . PHP_EOL;
            
            
//             print $m->getName() . ' - ' . $m->getDeclaringClass()->getName() . "\n";
        }
        
        $phpcode .= "\tpublic function proxy_addFilter( \$f ) {" . PHP_EOL;
        $phpcode .= "\t\t\$this->proxyFilters[] = \$f;" . PHP_EOL;
        $phpcode .= "\t}" . PHP_EOL;
        
        $phpcode .= "\tprotected function proxy_executeFunction() {" . PHP_EOL;
        
//         $phpcode .= "var_export(get_class(\$this));";
//         $phpcode .= "var_export(\$this->callFuncName);";
//         $phpcode .= "var_export(\$this->callParams);";
        $phpcode .= "\t\t\$ohc = new \\core\\container\\ObjectHookCall( \$this, \$this->callFuncName, \$this->callParams );".PHP_EOL;
        $phpcode .= "\t\t\$ohc->setFilters( \$this->proxyFilters );".PHP_EOL;
        $phpcode .= "\t\treturn \$ohc->next();".PHP_EOL;
        
        $phpcode .= "\t}" . PHP_EOL;
        
        
        
        $phpcode .= '}'.PHP_EOL;
        
//         if ( strpos($class, 'VisitorService') !== false) {
//             print $phpcode;exit;
//         }
        
        
        if (is_debug()) {
            eval( $phpcode );
        }
        else {
            // the browser does simultaneous requests which causes the same proxy to be generated by different processes.
            // problem is that 1 proces can write (partial), while another truncates... so lock files...
            
            // open file & lock. db_lock() can't be used here, not always a db-connection
            $fp = fopen( $path, 'c' );
            
            // get lock
            if( flock($fp, LOCK_EX|LOCK_NB) == false ) {
                // failed? => wait for it & skip generation, already done by another process... 
                flock($fp, LOCK_EX);
                
                require_once( $path );
                fclose($fp);
                
                return;
            }
            
            
            // truncate file
            ftruncate($fp, 0);
            
            fwrite( $fp, '<?php' . PHP_EOL . $phpcode );
            fflush( $fp );
            
            eval( $phpcode );       // use eval() - was require_once($path) before - this works also on winnt
            
            flock( $fp, LOCK_UN );
            fclose( $fp );
            
        }
    }
    
    
}


