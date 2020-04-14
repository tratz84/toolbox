<?php


namespace base\model;


class Note extends base\NoteBase {

    
    
    public function getSummary() {
        $t = $this->getShortNote();
        if (trim($t) != '') {
            return $t;
        }
        
        $t = trim($this->getLongNote());
        $lines = explode("\n", $t);
        
        foreach($lines as $l) {
            $t = $t . $l;
            
            if (strlen($t) > 80) {
                $t = limit_text($t, 80);
                break;
            }
        }
        
        return $t;
    }
    
    
    public function getEditedFormat($f='d-m-Y H:i;s') {
        return format_datetime($this->getEdited(), $f);
    }

}

