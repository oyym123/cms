<?php

namespace backend\controllers;

use common\models\Base;
use common\models\DomainTpl;
use common\models\Qiniu;
use common\models\Tools;
use common\models\WhiteArticle;
use Yii;
use common\models\Template;
use common\models\search\TemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
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
     * Lists all Template models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Template model.
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
     * Creates a new Template model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Template();
        $post = Yii::$app->request->post()['Template'];
        if ($model->load(Yii::$app->request->post())) {
            if (isset($post['php_func']) && !empty($post['php_func'])) {
                if (Yii::$app->user->identity->id != 1) {
                    Yii::$app->getSession()->setFlash('error', '请联系超级管理员填写PHP_FUNC');
                    return $this->redirect(['create', 'model' => $model]);
                }
            }

            if (!Tools::contentCheck($model->content)) {
                Yii::$app->getSession()->setFlash('error', '有敏感词！请重新提交');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            if (!empty($_FILES['Template']['name']['img'])) {
                //标题图片处理
                $imgInfo = (new Qiniu())->fileUpload('Template', 'aks-img01', 1, 1, 'img');
                $model->img = $imgInfo['url'];
            }

            //判断是否有 重复
            $enName = Template::find()->where([
                'en_name' => $post['en_name'],
            ])->one();

            if (!empty($enName)) {
                Yii::$app->getSession()->setFlash('error', '英文名必须唯一');
                return $this->redirect(['create', 'model' => $model]);
            }

            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Template model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post()['Template'];
        $old = $model;

        if ($model->load(Yii::$app->request->post())) {
            if (isset($post['php_func']) && !empty($post['php_func'])) {
                if (Yii::$app->user->identity->id != 1) {
                    Yii::$app->getSession()->setFlash('error', '请联系超级管理员填写PHP_FUNC');
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }

            if (!Tools::contentCheck($model->content)) {
                Yii::$app->getSession()->setFlash('error', '有敏感词！请重新提交');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            if (!empty($_FILES['Template']['name']['img'])) {
                //标题图片处理
                $imgInfo = (new Qiniu())->fileUpload('Template', 'aks-img01', 1, 1, 'img');
                $model->img = $imgInfo['url'];
            } else {
                $model->img = $this->findModel($id)->img;
            }

            //判断是否有 重复
            $enName = Template::find()->where([
                'en_name' => $post['en_name'],
            ])->one();

            if (!empty($enName) && $enName->en_name != $model->en_name) {
                Yii::$app->getSession()->setFlash('error', '英文名必须唯一');
                return $this->redirect(['update', 'id' => $model->id]);
            }

            if ($model->save(false)) {
                //更新模板
                DomainTpl::setTmp(0, $model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Template model.
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
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /** 获取模板 */
    public function actionGetTemplate()
    {
        $q = Yii::$app->request->get('q', '');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!$q) {
            return $out;
        }

        $data = Template::find()
            ->where([
                'status' => Base::STATUS_BASE_NORMAL,
                'type' => Template::TYPE_CUSTOMIZE
            ])
            ->select('id, en_name as text,name')
            ->andFilterWhere(['like', 'en_name', $q])
            ->limit(30)
            ->asArray()
            ->all();

        foreach ($data as &$item) {
            $item['text'] = '<strong>' . $item['text'] . '</strong>';
        }
        $out['results'] = array_values($data);
        return $out;
    }

    /**
     * 获取最新名称
     */
    public function actionGetName()
    {
        $type = Yii::$app->request->get('type');
        $cate = Yii::$app->request->get('cate');

        $lastName = Template::find()
            ->where([
                'cate' => $cate,
                'type' => $type
            ])->orderBy('id desc')->one();


        if (!empty($lastName)) {
            $arr = explode('_', $lastName->name);
            $num = end($arr);

            $name = Template::getType($type);
            $enName = Template::getEnType($type);
            if ($cate == Template::CATE_PC) {
                $name = $name . '_' . ($num + 1);
                $enName = $enName . '_' . ($num + 1);
            } else {
                $name = 'm_' . $name . '_' . ($num + 1);
                $enName = 'm_' . $enName . '_' . ($num + 1);
            }
        } else {
            $name = Template::getType($type);
            $enName = Template::getEnType($type);
            if ($cate == Template::CATE_PC) {
                $name = $name . '_1';
                $enName = $enName . '_1';
            } else {
                $name = 'm_' . $name . '_1';
                $enName = 'm_' . $enName . '_1';
            }
        }

        $name = [
            'en_name' => $name,
            'name' => $enName,
        ];
        exit(json_encode($name));
    }
}
