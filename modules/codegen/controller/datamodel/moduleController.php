<?php


use core\controller\BaseController;

class moduleController extends BaseController {
    
    
    public function action_index() {
        
        $mds = \core\Context::getInstance()->getModuleDirs();
        $this->modules = array();
        foreach($mds as $md) {
            $files = list_files($md, ['recursive' => false, 'dironly' => true]);
            
            foreach($files as $f) {
                $this->modules[] = array(
                    'module_dir' => realpath($md),
                    'module' => $f
                );
            }
        }
        usort($this->modules, function($o1, $o2) {
            return strcmp($o1['module'], $o2['module']);
        });
        
        return $this->render();
    }
    
    
}
