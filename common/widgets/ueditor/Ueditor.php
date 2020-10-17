<?php

namespace common\widgets\ueditor;

use common\models\BlackArticle;
use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;

use common\widgets\ueditor\assets\UeditorAsset;


class Ueditor extends InputWidget
{
    /**
     * 编辑器传参配置(配置查看百度编辑器（ueditor）官方文档)
     */
    public $options = [];

    /**
     * 编辑器默认基础配置
     */
    public $_init;

    public function init()
    {
        $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;

        $this->_init = [
            'serverUrl' => Url::to(['/ueditor']),
            'lang' => (strtolower(\Yii::$app->language) == 'en-us') ? 'zh-cn' : 'zh-cn',
        ];
        $this->options = ArrayHelper::merge($this->_init, $this->options);
        //parent::init();
    }

    public function run()
    {
        if ($this->hasModel()) {
            $this->model->content = nl2br($this->model->content);
            $this->registerClientScript($this->model);
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);
        } else {
            return Html::textarea($this->id, $this->value, ['id' => $this->id]);
        }
    }
    
//    public function run()
//    {
//        if ($this->hasModel()) {
//            $imgView = '?imageView2/1/w/240/h/180';
//            if ($this->model->type == BlackArticle::TYPE_ZUO_WEN_WANG) {
//                $this->registerClientScript($this->model);
//            }
//
//            if ($this->model->type == BlackArticle::TYPE_DOC_TXT) {
//                //前面转换
//                $upArr = [
//                    '一，', '二，', '三，', '四，', '五，',
//                    '一、', '二、', '三、', '四、', '五、',
//                    '一,', '二,', '三,', '四,', '五,',
//                    '1、', '2、', '3、', '4、', '5、',
//                    '1，', '2，', '3，', '4，', '5，',
//                    '1,', '2,', '3,', '4,', '5,'
//                ];
//
//                //后面转换
//                $downArr = ['?', '？', '！ ', '？ ', '。 ', '! ',];
//                $replaceArrUp = $replaceArrDown = [];
//
//                foreach ($upArr as $item) {
//                    $replaceArrUp[] = '<br>' . $item;
//                }
//
//                $this->model->content = str_replace($upArr, $replaceArrUp, $this->model->content);
//
//                foreach ($downArr as $item) {
//                    $replaceArrDown[] = $item . '<br>';
//                }
//                $this->model->content = str_replace($downArr, $replaceArrDown, $this->model->content);
//                $this->registerClientScript();
//            }
//
//            if ($this->model->type == BlackArticle::TYPE_SOUGOU_WEIXIN) {
//                $this->model->content = preg_replace("@<script(.*?)><\/script>@is", "", $this->model->content);
//                if (strpos($this->model->content, '<div id="js_article" class="rich_media">') !== false) {
//                    $content = $this->model->content;
//                    preg_match('@<div id="js_article" class="rich_media">(.*)?   <div class="function_mod function_mod_index"@s', $content, $contentInfo);
//                    $content = str_replace('data-src="', 'src="', $contentInfo[0]);
//                    $this->model->content = $content;
//                }
//                $this->registerClientScript($this->model);
//            }
//
//            if ($this->model->type == BlackArticle::TYPE_DOC_WORD) {
//                $this->model->content = preg_replace("@<script(.*?)><\/script>@is", "", $this->model->content);
//                $this->model->content = preg_replace("@(.*)?</head><body>@", '', $this->model->content);
//                $images = json_decode($this->model->image_urls, true);
//                if (!empty($images)) {
//                    foreach ($images as $img) {
//                        $this->model->content = str_replace($img, \Yii::$app->params['QiNiuHost'] . 'wordImg/' . $img, $this->model->content);
//                    }
//                }
//                $this->registerClientScript($this->model);
//            }
//
//            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);
//        } else {
//            return Html::textarea($this->id, $this->value, ['id' => $this->id]);
//        }
//    }

    /**
     * 注册Js
     */
    protected function registerClientScript($model = null)
    {
        UEditorAsset::register($this->view);
        $options = Json::encode($this->options);
        if ($model && $model->status != BlackArticle::STATUS_INIT) {
            //主要功能 ：判断是否渲染
            $script = "  UE.delEditor('" . $this->id . "', " . $options . "); 
                     ue= UE.getEditor('" . $this->id . "', " . $options . ")                     
                     ue.ready(function() {
                     console.log(ue.hasContents());
                         if(ue.hasContents()==false){     
                               ue.addListener(\"ready\", function () {
        　                   　// editor准备好之后才可以使用
                              ue.setContent('" . $model->content . "');
                                }); 
                          }
                        }); ";
        } else {
            $script = "ue= UE.getEditor('" . $this->id . "', " . $options . ")";
        }
        $this->view->registerJs($script, View::POS_READY);
    }
}
