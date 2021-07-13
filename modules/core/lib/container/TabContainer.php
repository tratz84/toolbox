<?php

namespace core\container;


class TabContainer {
    
    protected $source;
    
    protected $tabs = array();
    

    public function __construct($source) {
        $this->setSource($source);
    }
    
    public function getSource() { return $this->source; }
    public function setSource($source) { $this->source = $source; }
    
    /**
     * 
     * @param string $title
     * @param string $content
     * @param number $prio
     * @param array $opts - extra options, ie:
     *                              - 'name'
     */
    public function addTab($title, $content, $prio=10, $opts=array()) {
        $tab = array(
            'title' => $title,
            'content' => $content,
            'prio' => $prio
        );
        
        $tab = array_merge($tab, $opts);
        
        $this->tabs[] = $tab;
    }
    
    public function getTabs() { return $this->tabs; }
    public function getTabCount() { return count($this->tabs); }
    public function hasTabs() { return count($this->tabs) > 0 ? true : false; }
    
    public function render() {
        if (count($this->tabs) == 0) {
            return;
        }
        
        usort($this->tabs, function($t1, $t2) {
            if ($t1['prio'] == $t2['prio'])
                return strcmp($t1['title'], $t2['title']);
            
            return ($t1['prio'] - $t2['prio'])*100;
        });
        
        $html = '';
        
        // print tabs
        $html .= '<nav>' . PHP_EOL;
        $html .= '<div class="nav nav-tabs" id="nav-tab" role="tablist">' . PHP_EOL;
        foreach($this->tabs as $x => $tab) {
            $slug = slugify($tab['title']);
            
            $tab_name = isset($tab['name']) ? $tab['name'] : $slug;
            
            $html .= '<a class="nav-item nav-link '.($x==0?'active':'').'" id="nav-'.$tab_name.'-tab" data-tab-name="'.esc_attr($tab_name).'" data-bs-toggle="tab" role="tab" aria-controls="'.$tab_name.'" href="#nav-'.$tab_name.'" aria-selected="'.($x==0?'true':'false').'">'.esc_html($tab['title']).'</a>' . PHP_EOL;
        }
        $html .= '</div>' . PHP_EOL;
        $html .= '</nav>' . PHP_EOL;
        
        
        // content
        $html .= '<div class="tab-content" id="nav-tabContent">' . PHP_EOL;
        foreach($this->tabs as $x => $tab) {
            $slug = slugify($tab['title']);
            $tab_name = isset($tab['name']) ? $tab['name'] : $slug;
            
            $html .= '<div class="tab-pane '.($x==0?'show active':'').' tab-name-'.$tab_name.'" id="nav-'.$tab_name.'" role="tabpanel" aria-labelledby="'.$tab_name.'-tab">' . PHP_EOL;
            $html .= $tab['content'] . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        }
        
        $html .= '</div>' . PHP_EOL;
        
        print $html;
    }
    
}

