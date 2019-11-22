<?= "<?php" ?>


use core\controller\IndexTableBaseController;

class <?= $controller_name ?> extends IndexTableBaseController {
    
    protected $daoClass = null;
    protected $query = null;
    protected $indexTableColumns = array();
    protected $exportColumns = array();
    protected $htmlColumns = array();
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    public function renderRow($row) {
        
    }
    
    public function action_search() {
        
        parent::search();
    }
    
    
    
}

