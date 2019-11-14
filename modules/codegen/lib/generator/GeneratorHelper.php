<?php

namespace codegen\generator;

use core\forms\CheckboxField;
use core\forms\ColorPickerField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\HiddenField;
use core\forms\WidgetContainer;
use codegen\form\widgetoptions\DefaultWidgetOptionsForm;
use codegen\form\widgetoptions\CheckboxOptionsForm;
use codegen\form\widgetoptions\ContainerOptionsForm;
use codegen\form\widgetoptions\SelectOptionsForm;
use codegen\form\widgetoptions\HiddenOptionsForm;
use codegen\form\widgetoptions\ListEditWidgetsOptionsForm;
use codegen\form\widgetoptions\TextareaOptionsForm;
use core\forms\TextareaField;

class GeneratorHelper {
    
    
    public static function getFormClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'Form.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\forms\BaseForm::class, ['recursive' => true] );
        
        return $classes;
    }
    
    public static function getFormClassesMap() {
        $classes = self::getFormClasses();
        
        $map = array();
        foreach($classes as $cl) {
            $map[$cl['class']] = $cl['class'];
        }
        
        return $map;
    }
    

    public static function getFieldClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'Field.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\forms\BaseWidget::class, ['recursive' => true] );
        
        return $classes;
    }
    
    public static function getFieldClassesMap() {
        $classes = self::getFieldClasses();
        
        $map = array();
        foreach($classes as $cl) {
            $map[$cl['class']] = $cl['class'];
        }
        
        return $map;
    }
    

    public static function getListEditWidgetClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'ListEdit.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\forms\ListEditWidget::class, ['recursive' => false] );
        
        return $classes;
    }
    
    
    public static function getWidgets() {
        $formWidgets = array();
        
        $formWidgets[] = array(
            'type' => 'container',
            'class' => WidgetContainer::class,
            'editor' => ContainerOptionsForm::class,
            'label' => 'container'
        );

        $formWidgets[] = array(
            'type' => 'widget',
            'class' => HiddenField::class,
            'editor' => HiddenOptionsForm::class,
            'label' => 'Hidden field'
        );
        
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => TextField::class,
            'label' => 'Textfield'
        );
        
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => TextareaField::class,
            'editor' => TextAreaOptionsForm::class,
            'label' => 'Textarea-field'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => CheckboxField::class,
            'editor' => CheckboxOptionsForm::class,
            'label' => 'Checkbox'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => SelectField::class,
            'editor' => SelectOptionsForm::class,
            'label' => 'Select'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => ColorPickerField::class,
            'label' => 'Color picker'
        );
        
        
        $lewClasses = self::getListEditWidgetClasses();
        foreach($lewClasses as $lew) {
            $formWidgets[] = array(
                'type' => 'widget',
                'class' => $lew['class'],
                'editor' => ListEditWidgetsOptionsForm::class,
                'label' => $lew['class']                        // TODO.. set to description or something?
            );
        }
        
        
        $formWidgets = apply_filter('form-generator-form-widgets', $formWidgets);
        
        // set defaults
        for($x=0; $x < count($formWidgets); $x++) {
            if (isset($formWidgets[$x]['type']) == false)
                $formWidgets[$x]['type'] = 'widget';
        }
        
        return $formWidgets;
    }
    
    
    
}