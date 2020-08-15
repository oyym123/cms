<?php

namespace library\pepper;

/**
 * Class Notice
 *
 * @package app\library
 */
class ErrCode
{
    //模块 6位状态码
    public static function Show($errorCode)
    {
        $list = [
            '-1' => '失败',
            '000000' => '操作失败。',
            '000001' => '无效的参数。',
            '000002' => '',
            '000003' => '操作成功。',
            '000004' => '操作失败。',
            '000005' => '参数错误!',
            '000006' => '没有获取到数据!',
            '000007' => '数据已存在!',

        ];

        $error = '错误未被定义。';

        return $list[$errorCode] ?? $error;
    }
}