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

    public $file;


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


    /** 移动文件,并且返回服务器保存的地址 */
    public function moveFile($fileInfo, $uploadPath = 'uploads', $flag = true, $allowExt = array('jpeg', 'jpg', 'png', 'gif'), $maxSize = 2097152)
    {
        //判断错误号,只有为0或者是UPLOAD_ERR_OK,没有错误发生，上传成功
        if ($fileInfo['error'] > 0) {
            //注意！错误信息没有5
            switch ($fileInfo['error']) {
                case 1:
                    $mes = '上传文件超过了PHP配置文件中upload_max_filesize选项的值';
                    break;
                case 2:
                    $mes = '超过了HTML表单MAX_FILE_SIZE限制的大小';
                    break;
                case 3:
                    $mes = '文件部分被上传';
                    break;
                case 4:
                    $mes = '没有选择上传文件';
                    break;
                case 6:
                    $mes = '没有找到临时目录';
                    break;
                case 7:
                    $mes = '文件写入失败';
                    break;
                case 8:
                    $mes = '上传的文件被PHP扩展程序中断';
                    break;
            }
            echo '<pre>';
            print_r($fileInfo);
            exit;
            exit($mes);
            return false;
        }
        $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);

        //$allowExt=array('jpeg','jpg','png','gif');
        //检测上传文件的类型
        if (!in_array($ext, $allowExt)) {
            exit ('非法文件类型');
        }
        //检测上传文的件大小是否符合规范
        //$maxSize = 2097152;//2M
        if ($fileInfo['size'] > $maxSize) {
            exit('上传文件过大');
        }
        //检测图片是否为真实的图片类型
        //$flag=true;
        if ($flag) {
            if (!getimagesize($fileInfo['tmp_name'])) {
                exit('不是真实的图片类型');
            }
        }
        //检测是否是通过HTTP POST方式上传上来
        if (!is_uploaded_file($fileInfo ['tmp_name'])) {
            exit ('文件不是通过HTTP POST方式上传上来的');
        }

        //$uploadPath='uploads';
        //如果没有这个文件夹，那么就创建一个
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
            chmod($uploadPath, 0777);
        }

        //新文件名唯一
        $uniName = Tools::uniqueName($ext);
        $destination = $uploadPath . '/' . $uniName;
        //@符号是为了不让客户看到错误信息
        if (!@move_uploaded_file($fileInfo['tmp_name'], $destination)) {
            exit('文件移动失败');
        }
        //echo '文件上传成功';
        //return array(
        //    'newName'=>$destination,
        //    'size'=>$fileInfo['size'],
        //    'type'=>$fileInfo['type']
        //);
        return $destination;
    }


    /** 清理数据 */
    public function cleanInfo($file, $name)
    {
        $data = [];
        foreach ($file as $key => $item) {
            $data[$key] = $item[$name];
        }
        return $data;
    }
}
