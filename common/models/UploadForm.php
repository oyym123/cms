<?php


namespace common\models;

use yii\base\Model;
use Yii;
use yii\web\UploadedFile;


/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model

{

    /**
     * @var UploadedFile file attribute
     */

    public $title_img;


    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            [['file'], 'file', 'maxFiles' => 100], // <--- here!
            [['file'], 'file', 'skipOnEmpty' => false],
            [['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png',],
        ];
    }

    /** 获取所有文件 */
    public function getFiles($name)
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstances($model, $name);
            if ($model->file && $model->validate()) {
                $arr = [];
                foreach ($model->file as $file) {
                    $arr[] = $file;
//                    $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
                }
            }
        }
        return $arr;
    }

    /** 获取单个文件 */
    public function getFile($name)
    {

    }
}