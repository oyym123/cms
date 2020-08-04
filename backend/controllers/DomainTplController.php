<?php

namespace backend\controllers;

use Yii;
use common\models\DomainTpl;
use common\models\search\DomainTplSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DomainTplController implements the CRUD actions for DomainTpl model.
 */
class DomainTplController extends Controller
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
     * Lists all DomainTpl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DomainTplSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DomainTpl model.
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
     * Creates a new DomainTpl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DomainTpl();
        $post = Yii::$app->request->post()['DomainTpl'];
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($post['t_customize'])) {
                $model->t_customize = implode(',', $post['t_customize']);
            }

            //判断是否有 重复
            $res = DomainTpl::find()->where([
                'domain_id' => $post['domain_id'],
                'column_id' => $post['column_id'],
                'cate' => $post['cate'],
            ])->one();

            if (!empty($res)) {
                Yii::$app->getSession()->setFlash('error', '该类目已经存在模组，请去找到那个模组修改！');
                return $this->redirect(['create', 'model' => $model]);
            }

            //模组套装更换
            if ($post['tpl_id'] > 0) {
                list($code, $msg) = (new DomainTpl())->changeTpl($model, $post['tpl_id']);
                if ($code < 0) {
                    Yii::$app->getSession()->setFlash('error', $msg);
                    return $this->redirect(['create', 'model' => $model]);
                } else {
                    $model = $msg;
                }
            }

            if ($model->save(false)) {
                //更新模板
                DomainTpl::setTmp($model->domain_id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DomainTpl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post()['DomainTpl'];

        if ($model->load(Yii::$app->request->post())) {
            //判断是否有 重复
            $res = DomainTpl::find()->where([
                'domain_id' => $post['domain_id'],
                'column_id' => $post['column_id'],
                'cate' => $post['cate'],
            ])->one();

            if (!empty($res) && $res->id != $id) {
                Yii::$app->getSession()->setFlash('error', '该类目已经存在模组，请去修改！');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            //模组套装更换
            if ($post['tpl_id'] > 0) {
                list($code, $msg) = (new DomainTpl())->changeTpl($model, $post['tpl_id']);
                if ($code < 0) {
                    Yii::$app->getSession()->setFlash('error', $msg);
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    $model = $msg;
                }
            }

            if ($model->save(false)) {
                //更新模板
                DomainTpl::setTmp($model->domain_id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DomainTpl model.
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
     * Finds the DomainTpl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DomainTpl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DomainTpl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
