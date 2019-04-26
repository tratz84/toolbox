<?php


namespace webmail\model;


use webmail\mail\SpamCheck;

class FilterCondition extends base\FilterConditionBase {
    
    
    public function match(\PhpMimeMailParser\Parser $parser, $emlFile) {
        
        
        if ($this->getFilterType() == 'is_spam') {
            // TODO: spamcheck
            
            return SpamCheck::isSpam($emlFile);
        }
        
        
        
        if ($this->getFilterField() == 'subject') {
            $val = $parser->getHeader('subject');
            
            return $this->matchVal($val);
        }
        
        
        if ($this->getFilterField() == 'from') {
            $from = $parser->getAddresses('from');
            foreach($from as $f) {
                if (isset($f['display']) && $this->matchVal($f['display']))
                    return true;
                if (isset($f['address']) && $this->matchVal($f['address']))
                    return true;
            }
        }
        
        if ($this->getFilterField() == 'to') {
            $to = array_merge($parser->getAddresses('to'), $parser->getAddresses('cc'), $parser->getAddresses('bcc'));

            $originalTo = $parser->getHeader('X-Original-To');
            if ($originalTo && validate_email($originalTo)) {
                $to[] = array('address' => $originalTo);
            }

            foreach($to as $t) {
                if (isset($t['display']) && $this->matchVal($t['display']))
                    return true;
                if (isset($t['address']) && $this->matchVal($t['address']))
                    return true;
            }
        }
        
        
        return false;
    }
    
    protected function matchVal($val) {
        switch($this->getFilterType()) {
            case 'match' :
                return $val == $this->getFilterPattern();
                break;
            case 'starts_with' :
                return stripos($val, $this->getFilterPattern()) === 0 ? true : false;
                break;
            case 'ends_with' :
                return endsWith($val, $this->getFilterPattern()) ? true : false;
                break;
            case 'contains' :
                return stripos($val, $this->getFilterPattern()) !== false ? true : false;
                break;
            case 'regexp' :
                return @preg_match('/'.$this->getFilterPattern().'/', $val);
                break;
        }
    }

}

