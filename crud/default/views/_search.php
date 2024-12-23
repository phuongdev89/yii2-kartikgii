<?php

use phuongdev89\kartikgii\crud\Generator;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\View;

/**
 * @var View $this
 * @var Generator $generator
 */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var \yii\web\View $this
* @var \<?= ltrim($generator->searchModelClass, '\\') ?> $model
* @var \yii\widgets\ActiveForm $form
*/
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    <?php
    $count = 0;
    foreach ($generator->getColumnNames() as $attribute) {
        if (++$count < 6) {
            echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        } else {
            echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        }
    }
    ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
