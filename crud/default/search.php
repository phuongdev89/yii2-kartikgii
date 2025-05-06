<?php

use phuongdev89\kartikgii\crud\Generator;
use yii\helpers\StringHelper;
use yii\web\View;

/**
 * This is the template for generating CRUD search class of the specified model.
 *
 * @var View $this
 * @var Generator $generator
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
if ($generator->enableSearchDateRange) {
    foreach ($generator->getAllSearchDateRangeFields() as $searchDateRangeField) {
        $rules[] = "[
            ['$searchDateRangeField'],
            'match',
            'pattern' => '/^.+\s\-\s.+$/',
        ]";
    }
}
echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use phuongdev89\base\behaviors\DateRangeBehavior;

/**
* <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
*/
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>
{
<?php if ($generator->enableSearchDateRange): ?>
    <?php foreach ($generator->getAllSearchDateRangeFields() as $searchDateRangeField): ?>
        public $<?= $searchDateRangeField ?>_start;
        public $<?= $searchDateRangeField ?>_end;

    <?php endforeach; ?>
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
<?php if ($generator->enableSearchDateRange): ?>
    return [
    <?php foreach ($generator->getAllSearchDateRangeFields() as $searchDateRangeField): ?>
        [
        'class' => DateRangeBehavior::class,
        'attribute' => '<?= $searchDateRangeField ?>',
        'dateStartAttribute' => '<?= $searchDateRangeField ?>_start',
        'dateEndAttribute' => '<?= $searchDateRangeField ?>_end',
        ],
    <?php endforeach; ?>
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

<?php if ($generator->enableSearchDateRange): ?>
    <?php foreach ($generator->getAllSearchDateRangeFields() as $searchDateRangeField): ?>
        $query->andFilterWhere([
        '>=',
        '<?= $searchDateRangeField ?>',
        $this-><?= $searchDateRangeField ?>_start,
        ])
        ->andFilterWhere([
        '<',
        '<?= $searchDateRangeField ?>',
        $this-><?= $searchDateRangeField ?>_end,
        ]);
    <?php endforeach; ?>
<?php endif; ?>
return $dataProvider;
}
}
