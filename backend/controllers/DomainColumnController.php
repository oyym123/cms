<?php

namespace backend\controllers;

use common\models\DomainTpl;
use common\models\Fan;
use Yii;
use common\models\DomainColumn;
use common\models\search\DomainColumnSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DomainColumnController implements the CRUD actions for DomainColumn model.
 */
class DomainColumnController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DomainColumn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DomainColumnSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DomainColumn model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DomainColumn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DomainColumn();
        $post = Yii::$app->request->post()['DomainColumn'];
        if ($model->load(Yii::$app->request->post())) {
            $old = DomainColumn::find()->where([
                'domain_id' => $post['domain_id'],
                'name' => $post['name']
            ])->one();

            if (!empty($old)) {
                Yii::$app->getSession()->setFlash('error', '此域名已存在该栏目');
                return $this->redirect(['create', 'model' => $model]);
            }

            if ($model->save()) {
                //规则配置
                Fan::getRules($model->domain_id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DomainColumn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post()['DomainColumn'];
        if ($model->load(Yii::$app->request->post())) {

            $old = DomainColumn::find()->where([
                'domain_id' => $post['domain_id'],
                'name' => $post['name']
            ])->one();

            if (!empty($old) && $old->id != $id) {
                Yii::$app->getSession()->setFlash('error', '此域名已存在该栏目');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            if ($model->save()) {
                //规则配置
                Fan::getRules($model->domain_id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DomainColumn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DomainColumn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DomainColumn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DomainColumn::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
