<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/**
 * @var yii\web\View $this
 * @var phuongdev89\kartikgii\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";


?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
use yii\widgets\Pjax;

/**
* @var \yii\web\View $this
* @var \yii\data\ActiveDataProvider $dataProvider
<?= !empty($generator->searchModelClass) ? " * @var \\" . ltrim($generator->searchModelClass, '\\') . " \$searchModel\n" : '' ?>
*/

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <?php if ($generator->indexWidgetType === 'grid'): ?>
        <?= "<?php Pjax::begin(); echo " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
        ['class' => 'yii\grid\SerialColumn'],

        <?php
        $count = 0;
        if (($tableSchema = $generator->getTableSchema()) === false) {
            foreach ($generator->getColumnNames() as $name) {
                if (++$count < 6) {
                    echo "            '" . $name . "',\n";
                } else {
                    echo "            // '" . $name . "',\n";
                }
            }
        } else {
            foreach ($tableSchema->columns as $column) {
                if ($column->name == 'id') {
                    continue;
                }
                $format = $generator->generateColumnFormat($column);
                if ($column->type === 'date') {
                    $columnDisplay = "            ['attribute' => '$column->name','format' => ['date',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['date'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['date'] : 'd-m-Y']],";

                } elseif ($column->type === 'time') {
                    $columnDisplay = "            ['attribute' => '$column->name','format' => ['time',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['time'] : 'H:i:s A']],";
                } elseif ($column->type === 'datetime' || $column->type === 'timestamp') {
                    $columnDisplay = "            ['attribute' => '$column->name','format' => ['datetime',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],";
                } elseif ($generator->enableSearchDateRange && $column->name == 'user_id') {
                    $columnDisplay = "
                    [
                    'attribute' => 'user_id',
                    'label' => 'User ID',
                    'class' => \kartik\grid\DataColumn::class,
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => \$searchModel->user_id != '' ? \common\helpers\ArrayHelper::map(\backend\models\User::find()->andWhere(['id' => \$searchModel->user_id])->all(), 'id', 'username') : [],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['/ajax/users']),
                                'dataType' => 'json',
                                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
                            ],
                        ],
                    ],
                    'filterInputOptions' => ['placeholder' => 'Search User'],
                    'format' => 'html',
                    'value' => function (\$data) {
                        return \$data->user->username;
                    },
                ],
                    ";
                } elseif ($generator->enableSearchDateRange && $column->name == 'country_id') {
                    $columnDisplay = "
                    [
                        'class'               => \kartik\grid\DataColumn::class,
                        'attribute'           => 'country_id',
                        'filterType'          => \kartik\grid\GridView::FILTER_SELECT2,
                        'filter'              => ArrayHelper::map(\backend\models\Country::find()->all(), 'id', 'name'),
                        'filterWidgetOptions' => [
                            'options' => [
                                'prompt' => 'Please choose',
                            ],
                        ],
                        'value'               => function (\$data) {
                            return \$data->country->name;
                        },
                    ],";
                } elseif ($generator->enableSearchDateRange && $column->name == 'created_at') {
                    $columnDisplay = "
                    ['attribute' => 'created_at',
                'class' => \kartik\grid\DataColumn::class,
                'filterType' => \kartik\grid\GridView::FILTER_DATE_RANGE,
                'format'=>'datetime',
                'filterWidgetOptions' => [
                    'readonly' => 'readonly',
                    'convertFormat' => true,
                    'pluginOptions' => ['locale' => ['format' => 'Y-m-d'],'autoclose' => true],
                    'pluginEvents' => ['cancel.daterangepicker' => 'function(ev,picker){\$(this).val(\"\").trigger(\"change\");}'],
                ]],";
                } else {
                    $columnDisplay = "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',";
                }
                if (++$count < 6) {
                    echo $columnDisplay . "\n";
                } else {
                    echo "//" . $columnDisplay . " \n";
                }
            }
        }
        ?>
        [
        'class' => 'backend\grid\ActionColumn',
        'width' => '150px',
        ],
        ],
        'floatHeader' => false,
        'floatPageSummary' => false,
        'floatFooter' => false,
        'responsive' => false,
        'bordered' => true,
        'striped' => false,
        'condensed' => true,
        'hover' => true,
        'showPageSummary' => false,
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'persistResize' => false,
        'toggleDataOptions' => ['minCount' => 10],
        'toolbar' => [
        [
        'content' =>
        Html::a('<i class="fas fa-plus"></i>', ['create'], [
        'class' => 'btn btn-success',
        'title' => 'Create',
        ]),
        'options' => ['class' => 'btn-group mr-2 me-2'],
        ],
        '{export}',
        '{toggleData}',
        ],
        'panel' => [
        'type' => 'primary',
        ],
        ]); Pjax::end(); ?>
    <?php else: ?>
        <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]) ?>
    <?php endif; ?>

</div>
