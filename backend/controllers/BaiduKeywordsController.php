<?php

namespace backend\controllers;

use common\models\Keywords;
use Yii;
use common\models\BaiduKeywords;
use common\models\search\BaiduKeywordsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BaiduKeywordsController implements the CRUD actions for BaiduKeywords model.
 */
class BaiduKeywordsController extends Controller
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
     * Lists all BaiduKeywords models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BaiduKeywordsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BaiduKeywords model.
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
     * Creates a new BaiduKeywords model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BaiduKeywords();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BaiduKeywords model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BaiduKeywords model.
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
     * Finds the BaiduKeywords model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BaiduKeywords the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BaiduKeywords::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /** 获取标签 */
    public function actionGetTags()
    {
        $q = Yii::$app->request->get('q', '');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!$q) {
            return $out;
        }

        $data = BaiduKeywords::find()
            ->select('id, keywords as text,m_pv,pc_pv,competition')
            ->andFilterWhere(['like', 'keywords', $q])
            ->limit(50)
            ->asArray()
            ->orderBy('m_pv desc')
            ->all();
        foreach ($data as &$item) {
            $item['text'] = '<strong>' . $item['text'] . '</strong><strong style="position:relative;left:140px;color: rebeccapurple">&nbsp;&nbsp;&nbsp;M_PV：' . $item['m_pv'] . '&nbsp;&nbsp;&nbsp;&nbsp;PC_PV：' . $item['m_pv'] . '&nbsp;&nbsp;&nbsp;&nbsp;竞争度：' . $item['competition'] . '%</strong>';
        }
        $out['results'] = array_values($data);
        return $out;
    }
}
