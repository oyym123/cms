<?php

namespace backend\controllers;

use common\models\DbName;
use common\models\NewsClass;
use common\models\NewsClassTags;
use common\models\NewsData;
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
            if (empty($data['db_tags_id'])) {
                Yii::$app->getSession()->setFlash('error', '请填写标签！');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            $dbName = DbName::find()->where(['id' => $data['db_id']])->one();
            $data['db_name'] = $dbName->name;
            $data['db_tags_id'] = json_encode($data['db_tags_id']);
            $data['host_name'] = str_replace('m.', 'https://www.', $dbName->domain);

            $model1 = new UploadForm();

//                $model1->file = UploadedFile::getInstance($model1, 'title_img');

            $file = UploadedFile::getInstance($model1, "WhiteArticle[title_img]");
            echo '<pre>';
            print_r($file);
            exit;
            if ($model->file && $model->validate()) {
                return $model->file;
            }
            (new Qiniu())->fileUpload('title_img');


            //异步发送请求保存数据到CMS数据库
            $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':89/index.php?r=cms/set-article';
            $arr[] = $url;
            $res = Tools::curlPost($url, $data);
            if (strpos($res, 'success') === false) { //表示没有成功，则打印错误
                echo '<pre>';
                print_r($res);
                exit;
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
