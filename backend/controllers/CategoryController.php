<?php

namespace backend\controllers;

use common\models\Base;
use Yii;
use common\models\Category;
use common\models\search\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Category();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $data = $post['Category'];

            if ($data['pid'] != 0 && $data['pid2'] == 0) {
                $model->pid = $data['pid'];
                $model->level = 2;
            }

            //表示该分类是2级
            if ($data['pid2'] != 0 && $data['pid3'] == 0) {
                $model->pid = $data['pid2'];
                $model->level = 3;
            }

            //表示该分类是3级
            if ($data['pid3'] != 0) {
                $model->pid = $data['pid3'];
                $model->level = 4;
            }

            $model->pid2 = $data['pid'];
            $model->pid3 = $data['pid2'] ?: 0;
            $model->pid4 = $data['pid3'] ?: 0;
            $tname = '';
            $topName = Category::findOne($model->pid2);
            if ($topName) {
                $tname = $topName->en_name;
            }
            $model->en_name = $tname;
            $model->status = Base::STATUS_BASE_NORMAL;


            $name = Category::find()->where(['name' => $model->name])->one();
            if (!empty($name)) {
                Yii::$app->getSession()->setFlash('error', '该名称已存在！');
                return $this->redirect(['create', [
                    'model' => $model,
                ]
                ]);
            }

            if ($model->save(false)) {

            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ($model->load(Yii::$app->request->post())) {
            $data = $post['Category'];
            if ($data['pid'] != 0 && $data['pid2'] == 0) {
                $model->pid = $data['pid'];
                $model->level = 2;
            }

            //表示该分类是2级
            if ($data['pid2'] != 0 && $data['pid3'] == 0) {
                $model->pid = $data['pid2'];
                $model->level = 3;
            }

            //表示该分类是3级
            if ($data['pid3'] != 0) {
                $model->pid = $data['pid3'];
                $model->level = 4;
            }

            if ($model->pid == $model->id) {
                Yii::$app->getSession()->setFlash('error', '父类不能选择自己!');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            $model->pid2 = $data['pid'];
            $model->pid3 = $data['pid2'] ?: 0;
            $model->pid4 = $data['pid3'] ?: 0;

            $tname = '';

            $topName = Category::findOne($model->pid2);
            if ($topName) {
                $tname = $topName->en_name;
            }
            $model->status = Base::STATUS_BASE_NORMAL;
            $model->en_name = $tname;


            $name = Category::find()->where(['name' => $model->name])->one();
            if (!empty($name)) {
                Yii::$app->getSession()->setFlash('error', '该名称已存在！');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }


            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Category model.
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionGetCate()
    {
        $res = Category::find()->where([
            'pid' => Yii::$app->request->get('id'),
            'status' => Base::STATUS_BASE_NORMAL
        ])->asArray()->all();

        $arr = [];
        foreach ($res as $item) {
            $arr[0] = '请选择类型';
            $arr[$item['id']] = $item['name'];
        }
        echo json_encode($arr);
        exit;
    }

    /** 获取模板 */
    public function actionGetCategory()
    {
        $q = Yii::$app->request->get('q', '');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!$q) {
            return $out;
        }

        $data = Category::find()
            ->where([
                'status' => Base::STATUS_BASE_NORMAL,
            ])
            ->select('id,level,name as text,name,pid2')
            ->andFilterWhere(['like', 'name', $q])
            ->limit(30)
            ->asArray()
            ->all();

        foreach ($data as &$item) {
            $topName = Category::findOne($item['pid2']);
            if ($topName) {
                $tname = $topName->name;
            }
            $item['text'] = '<strong>' . $item['text'] . '</strong>        　等级:' . $item['level'] . '  　　顶级父类:' . $tname;
        }

        $out['results'] = array_values($data);
        return $out;
    }
}
