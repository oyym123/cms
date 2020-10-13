<?php

namespace backend\controllers;

use common\models\Base;
use Yii;
use common\models\Keywords;
use common\models\search\KeywordsSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * KeywordsController implements the CRUD actions for Keywords model.
 */
class KeywordsController extends Controller
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
     * Lists all Keywords models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KeywordsSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $model = Keywords::findOne(['id' => $id]);
            $output = '';
            $posted = current($_POST['Keywords']);
            $post = ['Keywords' => $posted];

            if ($model->load($post)) {

                isset($posted['keywords']) && $output = $model->keywords;
                if (isset($posted['status']) && $model->status == 0) {
                    $output = '<strong style="color: mediumvioletred">' . \common\models\Base::getBaseS($model->status) . '</strong>';
                } elseif (isset($posted['status'])) {
                    $output = '<strong style="color: blue">' . \common\models\Base::getBaseS($model->status) . '</strong>';
                }

                if (isset($posted['note'])) {
                    //查询
                    $keywords = Keywords::find()->select('keywords.id')
                        ->innerJoinWith('aizhanRules', 'aizhanRules.id = keywords.rules_id')
                        ->Where([
                            'keywords.status' => 1,
                            'keywords.note' => 0
                        ])
                        ->orderBy('search_num asc')
                        ->asArray()
                        ->all();

                    $ids = array_column($keywords, 'id');
                    $idsArr = array_slice($ids, 0, array_search($id,$ids));

                    Keywords::updateAll([
                        'check_time' => date('Y-m-d H:i:s'),
                        'note' => 100
                    ], ['in', 'id', $idsArr]);

                    $output = '<strong style="color: blue">检测时间:' . date('Y-m-d H:i:s') . '</strong>';
                }
                $model->save();
            }

            $out = Json::encode(['output' => $output, 'message' => '']);
            return $out;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Keywords model.
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
     * Creates a new Keywords model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Keywords();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Keywords model.
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
     * Deletes an existing Keywords model.
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
     * Finds the Keywords model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Keywords the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Keywords::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return array
     * 抓取爱站网的数据
     */
    public function actionCatch()
    {
        Keywords::catchKeyWords();
    }
}
