<?php

namespace backend\controllers;

use common\models\ArticleRules;
use common\models\Category;
use Yii;
use common\models\AizhanRules;
use common\models\search\AizhanRulesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AizhanRulesController implements the CRUD actions for AizhanRules model.
 */
class AizhanRulesController extends Controller
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
     * Lists all AizhanRules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AizhanRulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AizhanRules model.
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
     * Creates a new AizhanRules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AizhanRules();
        if ($model->load(Yii::$app->request->post())) {
            $category = Category::findOne($model->category_id);
            $rules = ArticleRules::find()->where(['category_id' => $model->category_id])->one();
            if ($rules) {
                $model->domain_id = $rules->domain_id;
                $model->column_id = $rules->column_id;
            }
            $site = AizhanRules::find()->where(['site_url' => $model->site_url])->one();

            if ($site) {
                Yii::$app->getSession()->setFlash('error', '该网址已经存在!');
                return $this->redirect(['create', 'model' => $model]);
            }

            $model->note = $category->intro;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AizhanRules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $category = Category::findOne($model->category_id);
            $rules = ArticleRules::find()->where(['category_id' => $model->category_id])->one();
            if ($rules) {
                $model->domain_id = $rules->domain_id;
                $model->column_id = $rules->column_id;
            }
            $model->note = $category->intro;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AizhanRules model.
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
     * Finds the AizhanRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AizhanRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AizhanRules::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
