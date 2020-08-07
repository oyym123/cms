<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fan_user".
 *
 * @property int $id
 * @property string $username
 * @property string|null $avatar 头像地址
 * @property string|null $nickname 昵称
 * @property string|null $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 */
class FanUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fan_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'created_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'avatar', 'nickname', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'avatar' => 'Avatar',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
        ];
    }

    /** 创建一个用户 */
    public static function createOne($data)
    {
        $old = self::find()->where(['username' => $data['username']])->one();
        if ($old) {
            return [-1, $data['username'] . '昵称已经重复'];
        }

        $model = new FanUser();
        $model->username = $data['username'];
        $model->avatar = $data['avatar'];
        $model->auth_key = '';
        $model->password_hash = md5(time());
        $model->email = '';
        $model->status = 10;
        $model->created_at = $data['created_at'];
        $model->verification_token = '';
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }

        return [1, $model];
    }


    /** 批量创建 */
    public function createMany()
    {
        set_time_limit(0);

        $data = TpUsers::find()->select('nickname,head_pic,reg_time')->where(['like', 'head_pic', 'https://wx.qlogo.cn'])->andWhere(['>', 'user_id', 3000])->addOrderBy('reg_time desc')->distinct('nickname')->limit(5000)->asArray()->all();

        $error = [];
        foreach ($data as $key => $item) {
            sleep(3);
            $img = Tools::curlGet($item['head_pic']);
            $res = file_get_contents('G:\132.png');
            $res2 = file_get_contents('G:\test.jpg');

            if ($img == $res || $img == $res2) { //不可显示的图片不保存
                echo $item['head_pic'] . '<br/>';
            } else {
                //标题图片存储七牛云
                list($codeImg, $msgImg) = (new Qiniu())->fetchFile($item['head_pic'], \Yii::$app->params['QiNiuBucketImg'], Tools::uniqueName('jpg'), 'userImg/');
                if ($codeImg < 0) {
                    $error[] = $msgImg;
                } else {
                    $saveData = [
                        'username' => $item['nickname'],
                        'avatar' => $msgImg,
                        'created_at' => $item['reg_time'],
                    ];

                    list($code, $msg) = self::createOne($saveData);
                    if ($code < 0) {
                        $error[] = $msg;
                    }
                }
            }
        }
        echo '<pre>';
        print_r($error);
    }
}
