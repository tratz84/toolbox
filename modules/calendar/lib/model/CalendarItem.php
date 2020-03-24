<?php


namespace calendar\model;


class CalendarItem extends base\CalendarItemBase {
    
    protected static $itemActions = null;

    public static function getItemActions() {
        
        if (self::$itemActions == null) {
            self::$itemActions = array();
            self::$itemActions['open']       = t('Open');
            self::$itemActions['postponed']  = t('Post poned');
            self::$itemActions['inprogress'] = t('In progress');
            self::$itemActions['done']       = t('Done');
            self::$itemActions['ignore']     = t('Ignore');
        }
        
        
        return self::$itemActions;
    }
    
    
    public function getItemAction() {
        $a = $this->getField('item_action');
        
        if ($a) {
            return $a;
        } else {
            return 'open';
        }
    }

}

