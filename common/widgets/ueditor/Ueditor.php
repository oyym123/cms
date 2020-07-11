<?php

namespace common\widgets\ueditor;

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
        $this->registerClientScript();
        if ($this->hasModel()) {

            //前面转换
            $upArr = [
                '一，', '二，', '三，', '四，', '五，',
                '一、', '二、', '三、', '四、', '五、',
                '一,', '二,', '三,', '四,', '五,',
                '1、', '2、', '3、', '4、', '5、',
                '1，', '2，', '3，', '4，', '5，',
                '1,', '2,', '3,', '4,', '5,'
            ];

            //后面转换
            $downArr = ['?', '？', '！ ', '？ ', '。 ', '! ',];
            $replaceArrUp = $replaceArrDown = [];

            foreach ($upArr as $item) {
                $replaceArrUp[] = '<br>' . $item;
            }

            $this->model->content = str_replace($upArr, $replaceArrUp, $this->model->content);

            foreach ($downArr as $item) {
                $replaceArrDown[] = $item . '<br>';
            }
            $this->model->content = str_replace($downArr, $replaceArrDown, $this->model->content);
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);
        } else {
            return Html::textarea($this->id, $this->value, ['id' => $this->id]);
        }
    }

    /**
     * 注册Js
     */
    protected function registerClientScript()
    {
        UEditorAsset::register($this->view);
        $options = Json::encode($this->options);
        $script = "UE.getEditor('" . $this->id . "', " . $options . ")";
        $this->view->registerJs($script, View::POS_READY);
    }
}
