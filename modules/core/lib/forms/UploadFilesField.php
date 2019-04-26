<?php


namespace core\forms;

class UploadFilesField extends BaseWidget {
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function renderAsText() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        if (count($this->getValue()) == 0)
            return '';
        
        $html .= '<div class="widget upload-field-widget upload-files-field-widget '.$extraClass.'">';
        $html .= '<input type="hidden" class="hidden-id" name="delete_'.$this->getName().'" value="" />';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<ul>';
        foreach($this->getValue() as $file) {
            $html .= '<li>';
            $html .= '<span><a href="javascript:void(0);" data-id="'.esc_attr($file->getId()).'" onclick="uploadFilesField_Click(this);">'.esc_html($file->getName()).'</a></span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget upload-field-widget upload-files-field-widget '.$extraClass.'">';
        $html .= '<input type="hidden" class="hidden-id" name="delete_'.$this->getName().'" value="" />';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<ul>';
        foreach($this->getValue() as $file) {
            $html .= '<li>';
            $html .= '<span><a href="javascript:void(0);" data-id="'.esc_attr($file->getId()).'" onclick="uploadFilesField_Click(this);">'.esc_html($file->getName()).'</a></span>';
            $html .= ' <a href="javascript:void(0);" data-id="'.esc_attr($file->getId()).'" onclick="uploadFilesFieldDelete_Click(this);"><span class="fa fa-remove remove-file"></span></a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        
        $html .= '<input type="file" name="'.esc_attr($this->getName()).'" onchange="this.form.submit()" />';
        
        $html .= '</div>';
        
        return $html;
    }
    
}
