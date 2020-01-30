<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use codegen\form\DAOTableSelectorForm;

class daoGeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $mds = \core\Context::getInstance()->getModuleDirs();
        $this->modules = array();
        foreach($mds as $md) {
            $files = list_files($md, ['recursive' => false, 'dironly' => true]);
            
            foreach($files as $f) {
                $this->modules[] = array(
                    'module_dir' => $md,
                    'module' => $f
                );
            }
        }
        usort($this->modules, function($o1, $o2) {
            return strcmp($o1['module'], $o2['module']);
        });
        
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        $p = module_file(get_var('mod'), '/');
        if ($p === false) {
            throw new InvalidStateException('Module not found');
        }
        
        $this->mod = get_var('mod');
        
        $cms = new \codegen\CodegenModuleSettings($this->mod);
        $cms->load();
        
        $this->form = new DAOTableSelectorForm( $cms );
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $arr = $this->form->asArray();
                $daotables = array();
                
                foreach($arr as $key => $val) {
                    $w = $this->form->getWidget($key);
                    if (!$val || !$w->getField('table_name'))
                        continue;
                    
                    $daotables[] = array(
                        'resource_name' => $w->getField('resource_name'),
                        'table_name' => $w->getField('table_name')
                    );
                }
                $cms->setVar('daotables', $daotables);
                $cms->save();
                
                redirect('/?m=codegen&c=daoGenerator&a=generate&mod='.urlencode($this->mod));
            }
        }
        
        return $this->render();
    }
    
    public function action_generate() {
        $p = module_file(get_var('mod'), '/');
        if ($p === false) {
            throw new InvalidStateException('Module not found');
        }
        
        $this->mod = get_var('mod');
        
        $cms = new \codegen\CodegenModuleSettings($this->mod);
        $cms->load();
        
        $daotables = $cms->getVar('daotables');
        
        ob_start();
        foreach($daotables as $t) {
            $table_name = $t['table_name'];
            $resource_name = $t['resource_name'];
            
            print "Generating DAO & model for: $table_name\n";
            $columns = queryList($resource_name, 'describe '.$table_name);
            
            $gen = new \core\generator\DAOGenerator($resource_name, $this->mod, $table_name, $columns);
            $gen->generate();
        }
        print "\nDone\n";
        $this->output = ob_get_clean();
        
        
        return $this->render();
    }
    
    
}


