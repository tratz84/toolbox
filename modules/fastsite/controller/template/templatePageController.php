<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use fastsite\template\TemplatePageData;

class templatePageController extends BaseController {
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function action_index() {
        $this->template = get_var('n');
        $this->file = get_var('f');
        
        $t = get_data_file('fastsite/templates/'.$this->template);
        if ($t === false) {
            throw new InvalidStateException('Template not found');
        }
        
        $this->tpd = new TemplatePageData($this->template, $this->file);
        $this->tpd->load();
        
        
        
        if (is_post()) {
            
            $this->tpd->setName( get_var('template_name') );
            $this->tpd->save();
            
            report_user_message('Changes saved');
        }
        
        return $this->render();
    }
    
    protected function listSnippets( $template ) {
        $t = get_data_file('fastsite/templates/'.$template.'/fastsite/');
        
        $files = list_files($t);
        
        $arr = array();
        foreach($files as $f) {
            if (strpos($f, 'snippet-') === 0 && file_extension($f) == 'php') {
                $snippetName = substr($f, 8, -4);
                
                $arr[] = $snippetName;
            }
        }
        
        return $arr;
    }
    
    
    public function action_snippet() {
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    
    
}
