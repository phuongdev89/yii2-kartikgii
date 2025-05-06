<?php
/**
 * @var View $this
 * @var ActiveForm $form
 * @var Generator $generator
 */

use phuongdev89\kartikgii\crud\Generator;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->registerCss("
    .field-generator-searchdatefields{display:none}
    .show{display:block!important}
    ");
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'moduleID');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'enablePjax')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'enableSearchDateRange')->checkbox();
echo $form->field($generator, 'searchDateFields', ['options' => ['class' => $generator->enableSearchDateRange ? 'show' : '']]);
$this->registerJs(
    <<<JS
        let moduleName = '{$generator->moduleName}';
        let searchmodelnamespace = $('#generator-searchmodelclass').val();
        searchmodelnamespace = searchmodelnamespace.replace(searchmodelnamespace.split("\\\").reverse()[0],'');
        let controllernamespace = $('#generator-controllerclass').val();
        controllernamespace = controllernamespace.replace(controllernamespace.split("\\\").reverse()[0],'');
        $(document).on('blur', '#generator-modelclass', function() {
            var class_name = $(this).val().split("\\\").reverse()[0];
            $('#generator-searchmodelclass').val(moduleName + "\\\models\\\search\\\" + class_name + 'Search');
            $('#generator-controllerclass').val(moduleName + "\\\controllers\\\"+ class_name + 'Controller');
        });
        $(document).on('change','#generator-enablesearchdaterange', function() {
            $('.field-generator-searchdatefields').toggleClass('show');
        });
JS
);
?>
