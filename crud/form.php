<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\gii\generators\crud\Generator $generator
 */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'moduleID');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'enableSearchDateRange')->checkbox();
echo $form->field($generator, 'messageCategory');
$this->registerJs(
    <<<JS
        let searchmodelnamespace=$('#generator-searchmodelclass').val();
        searchmodelnamespace = searchmodelnamespace.replace(searchmodelnamespace.split("\\\").reverse()[0],'');
        let controllernamespace=$('#generator-controllerclass').val();
        controllernamespace = controllernamespace.replace(controllernamespace.split("\\\").reverse()[0],'');
        $(document).on('blur', '#generator-modelclass', function () {
            var class_name = $(this).val().split("\\\").reverse()[0];
            $('#generator-searchmodelclass').val(searchmodelnamespace+class_name+'Search');
            $('#generator-controllerclass').val(controllernamespace+class_name+'Controller');
        });
JS
);
?>
