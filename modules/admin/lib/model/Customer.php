<?php


namespace admin\model;


class Customer extends base\CustomerBase {

    
    
    public function getExperimental() {
        $r = $this->getField('experimental');
        
        return apply_filter(self::class.'::getExperimental', $r);
    }

}

