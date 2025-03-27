<?php




use invoice\form\ArticleSelectField;

add_filter('form-generator-form-widgets', function($formWidgets) {
    
    
    $formWidgets[] = array(
        'type' => 'widget',
        'class' => ArticleSelectField::class,
        'label' => 'Article'
    );
    
    return $formWidgets;
});
    