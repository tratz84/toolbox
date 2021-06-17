<?php

namespace codegen\generator;

use base\forms\UserSelectWidget;
use codegen\form\widgetoptions\CheckboxOptionsForm;
use codegen\form\widgetoptions\ContainerOptionsForm;
use codegen\form\widgetoptions\DatePickerOptionsForm;
use codegen\form\widgetoptions\HiddenOptionsForm;
use codegen\form\widgetoptions\ListEditWidgetsOptionsForm;
use codegen\form\widgetoptions\ListFormWidgetsOptionsForm;
use codegen\form\widgetoptions\RadioOptionsForm;
use codegen\form\widgetoptions\SelectOptionsForm;
use codegen\form\widgetoptions\TextareaOptionsForm;
use codegen\form\widgetoptions\TimePickerOptionsForm;
use core\forms\CheckboxField;
use core\forms\ColorPickerField;
use core\forms\DatePickerField;
use core\forms\DateTimePickerField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\MonthField;
use core\forms\RadioField;
use core\forms\Select2Field;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\TimePickerField;
use core\forms\WeekField;
use core\forms\WidgetContainer;
use customer\forms\CustomerSelectWidget;
use core\forms\HtmlDateField;
use core\forms\HtmlDatetimeField;
use codegen\form\widgetoptions\HtmlDateOptionsForm;
use core\forms\FileField;

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
        
        usort($classes, function($c1, $c2) {
            return strcmp($c1['class'], $c2['class']);
        });
        
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
    
    
    public static function getDAOClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'DAO.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\db\DAOObject::class, ['recursive' => true] );
        
        return $classes;
    }
    

    public static function getListEditWidgetClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'ListEdit.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\forms\ListEditWidget::class, ['recursive' => false] );
        
        return $classes;
    }

    
    public static function getListFormWidgetClasses() {
        $pcm = new \codegen\parser\PhpCodeMeta();
        $pcm->parseFiles(['filter' => function($f ){
            return endsWith($f, 'ListForm.php');
        }]);
            
        $classes = $pcm->classesWithBaseClass( \core\forms\ListFormWidget::class, ['recursive' => false] );
        
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
            'class' => \core\forms\NumberField::class,
            'editor' => \codegen\form\widgetoptions\NumberOptionsForm::class,
            'label' => 'Number field'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => \core\forms\EuroField::class,
//             'editor' => \codegen\form\widgetoptions\NumberOptionsForm::class,
            'label' => 'Euro field'
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
            'class' => DatePickerField::class,
            'editor' => DatePickerOptionsForm::class,
            'label' => 'DatePicker'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => DateTimePickerField::class,
            'editor' => DatePickerOptionsForm::class,
            'label' => 'DateTimePicker'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => TimePickerField::class,
            'editor' => TimePickerOptionsForm::class,
            'label' => 'TimePicker'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => WeekField::class,
            'label' => 'WeekField'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => MonthField::class,
            'label' => 'MonthField'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => SelectField::class,
            'editor' => SelectOptionsForm::class,
            'label' => 'Select'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => Select2Field::class,
            'editor' => SelectOptionsForm::class,
            'label' => 'Select2'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => RadioField::class,
            'editor' => RadioOptionsForm::class,
            'label' => 'Radio'
        );
        
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => CustomerSelectWidget::class,
            'label' => 'Customer select'
        );
        
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => ColorPickerField::class,
            'label' => 'Color picker'
        );

        $formWidgets[] = array(
            'type' => 'widget',
            'class' => HtmlField::class,
//             'editor' => HtmlOptionsForm::class,
            'label' => 'Html-field'
        );

        $formWidgets[] = array(
            'type' => 'widget',
            'class' => HtmlDateField::class,
            'editor' => HtmlDateOptionsForm::class,
            'label' => 'HtmlDate-field'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => HtmlDatetimeField::class,
            'editor' => HtmlDateOptionsForm::class,
            'label' => 'HtmlDatetime-field'
        );
        
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => UserSelectWidget::class,
            'label' => 'User select'
        );
        $formWidgets[] = array(
            'type' => 'widget',
            'class' => FileField::class,
            'label' => 'File upload'
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
        
        $lfwClasses = self::getListFormWidgetClasses();
        foreach($lfwClasses as $lfw) {
            $formWidgets[] = array(
                'type' => 'widget',
                'class' => $lfw['class'],
                'editor' => ListFormWidgetsOptionsForm::class,
                'label' => $lfw['class']                        // TODO.. set to description or something?
            );
        }
        
        
        $formWidgets = apply_filter('form-generator-form-widgets', $formWidgets);
        
        // set defaults
        for($x=0; $x < count($formWidgets); $x++) {
            if (isset($formWidgets[$x]['type']) == false)
                $formWidgets[$x]['type'] = 'widget';
        }

        // sort, containers on top, ListEdit bottom, Widgets in the middel, sorted by name
        usort($formWidgets, function($o1, $o2) {
            if (endsWith($o1['type'], 'container') == true && endsWith($o2['type'], 'container') == false) {
                return -1;
            }
            if (endsWith($o1['type'], 'container') == false && endsWith($o2['type'], 'container') == true) {
                return 1;
            }
            
            if (endsWith($o1['class'], 'ListEdit') == true && endsWith($o2['class'], 'ListEdit') == false) {
                return 1;
            }
            if (endsWith($o1['class'], 'ListEdit') == false&& endsWith($o2['class'], 'ListEdit') == true) {
                return -1;
            }
            
            return strcmp($o1['label'], $o2['label']);
            
        });
        
        return $formWidgets;
    }
    
    
    
}