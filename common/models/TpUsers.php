<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tp_users".
 *
 * @property int $user_id 表id
 * @property int $is_engineer 是否工程师
 * @property int $engineer_status 工程师状态：0-禁用，1-正常
 * @property int|null $engineer_source
 * @property int|null $suppliers_id 门店Id
 * @property string $email 邮件
 * @property string $password 密码
 * @property string|null $paypwd 支付密码
 * @property int $sex 0 保密 1 男 2 女
 * @property int $birthday 生日
 * @property float $user_money 用户金额
 * @property float|null $frozen_money 冻结金额
 * @property float|null $distribut_money 分佣积累金额
 * @property int|null $underling_number 用户下线总数
 * @property int $pay_points 当前积分
 * @property int $address_id 默认收货地址
 * @property int $reg_time 注册时间
 * @property int $last_login 最后登录时间
 * @property string $last_ip 最后登录ip
 * @property string $qq QQ
 * @property string $mobile 手机号码
 * @property int $mobile_validated 是否验证手机
 * @property string|null $oauth 第三方来源 wx weibo alipay
 * @property string|null $openid 第三方唯一标示
 * @property string|null $unionid
 * @property string|null $head_pic 头像
 * @property int|null $province 省份
 * @property int|null $city 市区
 * @property int|null $district 县
 * @property int $email_validated 是否验证电子邮箱
 * @property string|null $nickname 第三方返回昵称
 * @property int|null $level 会员等级
 * @property float|null $discount 会员折扣，默认1不享受
 * @property float|null $total_amount 消费累计额度
 * @property int|null $is_lock 是否被锁定冻结
 * @property int|null $is_distribut 是否为分销商 0 否 1 是
 * @property int|null $first_leader 第一个上级
 * @property int|null $second_leader 第二个上级
 * @property int|null $third_leader 第三个上级
 * @property int|null $perpetual 是否是永久上下级关系，0不是，1是
 * @property string|null $token 用于app 授权类似于session_id
 * @property int $message_mask 消息掩码
 * @property string $push_id 推送id
 * @property int|null $distribut_level 分销商等级
 * @property string|null $wx_code 小程序码
 * @property string|null $wechat 微信号
 * @property string|null $short_url 推广二维码短连接
 * @property int|null $user_type 用户类型 1=普通用户，2=加盟工程师，3=有底薪工程师，4=无底薪工程师
 * @property string|null $device_tokens 设备信息号
 * @property string|null $order_mobile 订单手机号
 * @property string|null $cash_password 提现密码
 * @property string|null $latitude 纬度
 * @property string|null $longitude 经度
 * @property int|null $device_type 设备类型 1=安卓 2=苹果 10=其他
 * @property string|null $mini_openid 小程序openid
 * @property int|null $subscribe 是否关注 1=已关注 0=未关注
 */
class TpUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tp_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_engineer', 'engineer_status', 'engineer_source', 'suppliers_id', 'sex', 'birthday', 'underling_number', 'pay_points', 'address_id', 'reg_time', 'last_login', 'mobile_validated', 'province', 'city', 'district', 'email_validated', 'level', 'is_lock', 'is_distribut', 'first_leader', 'second_leader', 'third_leader', 'perpetual', 'message_mask', 'distribut_level', 'user_type', 'device_type', 'subscribe'], 'integer'],
            [['user_money', 'frozen_money', 'distribut_money', 'discount', 'total_amount'], 'number'],
            [['nickname'], 'string'],
            [['email'], 'string', 'max' => 60],
            [['password', 'paypwd'], 'string', 'max' => 32],
            [['last_ip'], 'string', 'max' => 15],
            [['qq', 'mobile'], 'string', 'max' => 20],
            [['oauth'], 'string', 'max' => 10],
            [['openid', 'unionid', 'short_url'], 'string', 'max' => 100],
            [['head_pic', 'wx_code', 'wechat', 'device_tokens', 'order_mobile', 'cash_password', 'latitude', 'longitude', 'mini_openid'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 64],
            [['push_id'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'is_engineer' => 'Is Engineer',
            'engineer_status' => 'Engineer Status',
            'engineer_source' => 'Engineer Source',
            'suppliers_id' => 'Suppliers ID',
            'email' => 'Email',
            'password' => 'Password',
            'paypwd' => 'Paypwd',
            'sex' => 'Sex',
            'birthday' => 'Birthday',
            'user_money' => 'User Money',
            'frozen_money' => 'Frozen Money',
            'distribut_money' => 'Distribut Money',
            'underling_number' => 'Underling Number',
            'pay_points' => 'Pay Points',
            'address_id' => 'Address ID',
            'reg_time' => 'Reg Time',
            'last_login' => 'Last Login',
            'last_ip' => 'Last Ip',
            'qq' => 'Qq',
            'mobile' => 'Mobile',
            'mobile_validated' => 'Mobile Validated',
            'oauth' => 'Oauth',
            'openid' => 'Openid',
            'unionid' => 'Unionid',
            'head_pic' => 'Head Pic',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'email_validated' => 'Email Validated',
            'nickname' => 'Nickname',
            'level' => 'Level',
            'discount' => 'Discount',
            'total_amount' => 'Total Amount',
            'is_lock' => 'Is Lock',
            'is_distribut' => 'Is Distribut',
            'first_leader' => 'First Leader',
            'second_leader' => 'Second Leader',
            'third_leader' => 'Third Leader',
            'perpetual' => 'Perpetual',
            'token' => 'Token',
            'message_mask' => 'Message Mask',
            'push_id' => 'Push ID',
            'distribut_level' => 'Distribut Level',
            'wx_code' => 'Wx Code',
            'wechat' => 'Wechat',
            'short_url' => 'Short Url',
            'user_type' => 'User Type',
            'device_tokens' => 'Device Tokens',
            'order_mobile' => 'Order Mobile',
            'cash_password' => 'Cash Password',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'device_type' => 'Device Type',
            'mini_openid' => 'Mini Openid',
            'subscribe' => 'Subscribe',
        ];
    }
}
