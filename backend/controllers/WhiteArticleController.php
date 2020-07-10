<?php

namespace backend\controllers;

use common\models\BaiduKeywords;
use common\models\DbName;
use common\models\NewsClass;
use common\models\NewsClassTags;
use common\models\NewsData;
use common\models\Publish;
use common\models\Qiniu;
use common\models\Tools;
use common\models\UploadForm;
use Yii;
use common\models\WhiteArticle;
use common\models\search\WhiteArticleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * WhiteArticleController implements the CRUD actions for WhiteArticle model.
 */
class WhiteArticleController extends Controller
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
     * Lists all WhiteArticle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WhiteArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WhiteArticle model.
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
     * Creates a new WhiteArticle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WhiteArticle();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WhiteArticle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = Yii::$app->request->post('WhiteArticle');

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($_FILES['WhiteArticle']['name']['title_img'])) {
                //标题图片处理
                $imgInfo = (new Qiniu())->fileUpload('WhiteArticle', 'aks-img01', 1, 1);
                $model->title_img = $imgInfo['url'];
            }

            //当文章有效时，则发布
            if ($data['status'] == WhiteArticle::STATUS_ENABLE) {
                if (empty($data['db_tags_id'])) {
                    Yii::$app->getSession()->setFlash('error', '请填写标签！');
                    return $this->redirect(['update', 'id' => $model->id]);
                }

                //获取第一个关键词 作为该文章的关键词
                $tagname = BaiduKeywords::find()->select('keywords')->where(['in', 'id', $data['db_tags_id']])->all();
                $data['db_tags_name'] = array_column($tagname, 'keywords');

                Publish::pushArticle($data);

                if (empty($model->history)) {
                    $oldHistory = [];
                } else {
                    //记录发布历史
                    $oldHistory = json_decode($model->history, true);
                }

                $dbName = DbName::find()->where(['id' => $data['db_id']])->one();
                $nowData = [
                    'databases' => $dbName->name,
                    'db_class_id' => $data['db_class_id'],
                ];

                $newHistory = array_merge($oldHistory, $nowData);
                $model->history = json_encode($newHistory);
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WhiteArticle model.
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
     * Finds the WhiteArticle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WhiteArticle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WhiteArticle::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetClass()
    {
        $res = NewsClass::find()->where([])->asArray()->all();
        $arr = [];
        foreach ($res as $item) {
            $arr[$item['classid']] = $item['classname'];
        }
        echo json_encode($arr);
        exit;
    }


}
