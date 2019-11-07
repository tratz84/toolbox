<?php

namespace codegen\generator;

use core\forms\CheckboxField;
use core\forms\ColorPickerField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\WidgetContainer;
use codegen\form\widgetoptions\DefaultWidgetOptionsForm;
use codegen\form\widgetoptions\CheckboxOptionsForm;
use codegen\form\widgetoptions\ContainerOptionsForm;
use codegen\form\widgetoptions\SelectOptionsForm;


class GeneratorHelper {
    
    
    public static function getWidgets() {
        $formWidgets = array();
        
        $formWidgets[] = array(
            'type' => 'container',
            'class' => WidgetContainer::class,
            'editor' => ContainerOptionsForm::class,
            'label' => 'container'
        );
        
        $formWidgets[] = array(
            'class' => TextField::class,
            'label' => 'Textfield'
        );
        $formWidgets[] = array(
            'class' => CheckboxField::class,
            'editor' => CheckboxOptionsForm::class,
            'label' => 'Checkbox'
        );
        $formWidgets[] = array(
            'class' => SelectField::class,
            'editor' => SelectOptionsForm::class,
            'label' => 'Select'
        );
        $formWidgets[] = array(
            'class' => ColorPickerField::class,
            'label' => 'Color picker'
        );
        
        
        $formWidgets = apply_filter('form-generator-form-widgets', $formWidgets);
        
        // set defaults
        for($x=0; $x < count($formWidgets); $x++) {
            if (isset($formWidgets[$x]['type']) == false)
                $formWidgets[$x]['type'] = 'widget';
        }
        
        return $formWidgets;
    }
    
    
    
}