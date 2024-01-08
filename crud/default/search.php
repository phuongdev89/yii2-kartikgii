<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating CRUD search class of the specified model.
 *
 * @var backend\components\View $this
 * @var backend\gii\crud\Generator $generator
 */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();
$needDateRange = $generator->enableSearchDateRange && in_array('created_at', $searchAttributes) && in_array('updated_at', $searchAttributes);
if ($needDateRange) {
    $rules[] = "[
        ['created_at'],
        'match',
        'pattern' => '/^.+\s\-\s.+$/',
    ]";
}
echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
* <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
*/
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>
{
<?php if ($needDateRange): ?>
    public $createdStart;
    public $createdEnd;
<?php endif; ?>

/**
* @inheritDoc
* @return array
*/
public function rules()
{
return [
<?= implode(",\n            ", $rules) ?>,
];
}

/**
* @inheritDoc
* @return array
*/
public function behaviors()
{
<?php if ($needDateRange): ?>
    return [
    [
    'class' => \common\behaviors\DateRangeBehavior::className(),
    'attribute' => 'created_at',
    'dateStartAttribute' => 'createdStart',
    'dateEndAttribute' => 'createdEnd',
    ],
    ];
<?php else: ?>
    return parent::behaviors();
<?php endif; ?>
}


/**
* @inheritDoc
* @return array
*/
public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

/**
* @param $params
* @return ActiveDataProvider
*/
public function search($params)
{
$query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();

$dataProvider = new ActiveDataProvider([
'query' => $query,
'sort' => [
'defaultOrder' => ['id' => SORT_DESC],
],
]);

if (!($this->load($params) && $this->validate())) {
return $dataProvider;
}

<?= implode("\n        ", $searchConditions) ?>

<?php if ($needDateRange): ?>
    $query->andFilterWhere([
    '>=',
    'created_at',
    $this->createdStart,
    ])
    ->andFilterWhere([
    '<',
    'created_at',
    $this->createdEnd,
    ]);
<?php endif; ?>
return $dataProvider;
}
}
