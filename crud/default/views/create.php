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

/**
* @var \yii\web\View $this
* @var \<?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = <?= $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
    <div class="page-header">
        <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
    </div>
    <?= "<?= " ?>$this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
