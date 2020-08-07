<?php


namespace frontend\controllers;


use yii\web\Controller;

class SwaggerController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0",
     *      title="SEO",
     *      description="模块接口",
     *      @OA\Contact(
     *          name="欧阳",
     *      )
     * )
     */
    public function actionIndex()
    {

        $projectRoot = \Yii::getAlias('@frontend');

        $swagger = \OpenApi\scan($projectRoot);
        $swagger = json_encode($swagger);
        $json_file = $projectRoot . '/web/swagger-docs/swagger.json';
        $is_write = file_put_contents($json_file, $swagger);
        if ($is_write == true) {
            $this->redirect('/swagger-ui/dist/index.html');
        }

    }
}