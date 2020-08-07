if (preg_match('/\d+/', $url, $arr)) { //获取id
$model = PushArticle::find()->select('title_img,content,title,intro,push_time')->where(['id' => $arr[0]])->asArray()->one();
list($layout, $render) = Fan::renderView(Template::TYPE_CUSTOMIZE);
$this->layout = $layout;
return $this->render($render, ['model' => $model]);
}