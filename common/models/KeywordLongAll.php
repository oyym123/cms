<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "keyword_long_all".
 *
 * @property string|null $keywors_name
 * @property int|null $day30_pingjun
 * @property int|null $day30_pc_and_m
 * @property int|null $day30_pc_rijun
 * @property int|null $day30_m_rijun
 * @property int|null $jingzheng
 * @property string|null $weidu
 * @property string|null $tag
 */
class KeywordLongAll extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'keyword_long_all';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day30_pingjun', 'day30_pc_and_m', 'day30_pc_rijun', 'day30_m_rijun', 'jingzheng'], 'integer'],
            [['keywors_name', 'weidu', 'tag'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'keywors_name' => 'Keywors Name',
            'day30_pingjun' => 'Day30 Pingjun',
            'day30_pc_and_m' => 'Day30 Pc And M',
            'day30_pc_rijun' => 'Day30 Pc Rijun',
            'day30_m_rijun' => 'Day30 M Rijun',
            'jingzheng' => 'Jingzheng',
            'weidu' => 'Weidu',
            'tag' => 'Tag',
        ];
    }

    /** 清洗数据 */
    public static function cleanData()
    {
        $data = self::find()->select('keywors_name as keywords')
            ->andWhere(['like', 'keywors_name', '英语'])
            ->andWhere(['between', 'day30_m_rijun', 0, 1])
            ->asArray()
            ->offset(0)
            ->limit(70000)
            ->all();
        return $data;
    }
}
