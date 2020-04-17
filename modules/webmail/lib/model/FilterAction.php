<?php


namespace webmail\model;


class FilterAction extends base\FilterActionBase {

    
    
    public function getMoveToFolderFilterActionValue () {
        if ($this->getFilterAction() == 'move_to_folder') {
            return $this->getFilterActionValue();
        }
        
        return null;
    }
    
    public function getSetActionFilterActionValue() {
        if ($this->getFilterAction() == 'set_action') {
            return $this->getFilterActionValue();
        }

        return null;
    }
    

}

