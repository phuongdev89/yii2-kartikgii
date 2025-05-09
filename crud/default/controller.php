<?php

use phuongdev89\kartikgii\crud\Generator;
use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;
use yii\web\View;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var View $this
 * @var Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/** @var ActiveRecordInterface $class */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
    use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
    use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\db\Exception;

/**
* <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
*/
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{

/**
* @return array
*/
public function behaviors()
{
return [
'verbs' => [
'class' => VerbFilter::class,
'actions' => [
'delete' => ['post'],
],
],
];
}

/**
* Lists all <?= $modelClass ?> models.
* @return string
*/
public function actionIndex()
{
<?php if (!empty($generator->searchModelClass)): ?>
    $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>;
    $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

    return $this->render('index', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
    ]);
<?php else: ?>
    $dataProvider = new ActiveDataProvider([
    'query' => <?= $modelClass ?>::find(),
    ]);

    return $this->render('index', [
    'dataProvider' => $dataProvider,
    ]);
<?php endif; ?>
}

/**
* Displays a single <?= $modelClass ?> model.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return string|\yii\web\Response
* @throws NotFoundHttpException|Exception
*/
public function actionView(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);

if ($model->load(Yii::$app->request->post()) && $model->save()) {
return $this->redirect(['view', 'id' => $model-><?= $generator->getTableSchema()->primaryKey[0] ?>]);
} else {
return $this->render('view', ['model' => $model]);
}
}

/**
* Creates a new <?= $modelClass ?> model.
* If creation is successful, the browser will be redirected to the 'view' page.
* @return string|\yii\web\Response
* @throws Exception
*/
public function actionCreate()
{
$model = new <?= $modelClass ?>;

if ($model->load(Yii::$app->request->post()) && $model->save()) {
return $this->redirect(['view', <?= $urlParams ?>]);
} else {
return $this->render('create', [
'model' => $model,
]);
}
}

/**
* Updates an existing <?= $modelClass ?> model.
* If update is successful, the browser will be redirected to the 'view' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return string|\yii\web\Response
* @throws NotFoundHttpException|Exception
*/
public function actionUpdate(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);

if ($model->load(Yii::$app->request->post()) && $model->save()) {
return $this->redirect(['view', <?= $urlParams ?>]);
} else {
return $this->render('update', [
'model' => $model,
]);
}
}

/**
* Deletes an existing <?= $modelClass ?> model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return \yii\web\Response
* @throws NotFoundHttpException
* @throws \Throwable
* @throws StaleObjectException
*/
public function actionDelete(<?= $actionParams ?>)
{
$this->findModel(<?= $actionParams ?>)->delete();

return $this->redirect(['index']);
}

/**
* Finds the <?= $modelClass ?> model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return <?= $modelClass ?> the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModel(<?= $actionParams ?>)
{
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
return $model;
} else {
throw new NotFoundHttpException('The requested page does not exist.');
}
}
}
