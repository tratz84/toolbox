<?php

namespace codegen\parser;

class PhpCodeMeta {
    
    protected $meta = null;
    
    public function parseFiles($opts=array()) {
        $this->meta = array();
        
        $mods = module_list();
        
        foreach($mods as $modname => $path) {
            $files = list_files($path . '/lib', ['recursive' => true]);
            
            // module not having a '/lib'-folder? => skip
            if ($files == false)
                continue;
            
            foreach($files as $f) {
                $p = $path.'/lib/'.$f;
                
                if (isset($opts['filter']) && $opts['filter']($p) == false) {
                    continue;
                }
                
                if (endsWith($p, '.php') && is_file($p)) {
                    $this->parse($p);
                }
            }
        }
    }
    
    
    public function parse($file) {
        $pcp = new PhpCodeParser();
        $pcp->parse($file);
        
        $class = $pcp->getClass();
        if (!$class) {
            return;
        }
        
        $m = array(
            'file' => $file,
            'class' => $pcp->getFullClassPath( $class['name'] )
        );
        
        if (isset($class['base'])) {
            $m['baseclass'] = $pcp->getFullClassPath( $class['base'] );
        }
        
        $this->meta[] = $m;
    }
    
    public function classesWithBaseClass($baseclassName, $opts=array()) {
        $arr = array();
        
        foreach($this->meta as $m) {
            if (isset($m['baseclass']) && $m['baseclass'] == $baseclassName) {
                $arr[] = $m;
                
                if (isset($opts['recursive']) && $opts['recursive']) {
                    $recur = $this->classesWithBaseClass( $m['class'] );
                    $arr = array_merge($arr, $recur);
                }
            }
        }
        
        
        return $arr;
    }
    
    
}
