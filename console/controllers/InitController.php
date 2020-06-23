<?php
namespace console\controllers;

use common\models\User;

class InitController extends \yii\console\Controller{

    public function actionAdd(){
        $username = $this->prompt("请输入用户：\n");
        $email    = $this->prompt("输入Email：\n");
        $password = $this->prompt("请输入密码：\n");

        $model = new User();
        $model->username = $username;
        $model->email = $email;
        $model->password = $password;
        if( !$model->save() ){
            foreach ($model->getErrors() as $errors){
                foreach ($errors as $e){
                    echo $e."\n";
                }
            }
        }
    }

}
