<?php

namespace common\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "domain_tpl".
 *
 * @property int $id
 * @property int|null $domain_id
 * @property int|null $template_id
 * @property int|null $column_id 分类id
 * @property int|null $type 模板类型
 * @property int|null $status 10=正常  20=禁用
 * @property int|null $user_id 创建人id
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class DomainTpl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain_tpl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'domain_id', 'column_id', 't_home', 't_common', 't_detail', 't_inside','t_tags', 't_list', 'cate',], 'required'],
            [['domain_id', 'template_id', 'column_id', 'type', 'status', 'user_id', 'tpl_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['en_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => '域名',
            'column_id' => '分类',
            'template_id' => '模板',
            't_home' => '首页',
            'tpl_id' => '套装ID  【选择套装后 后面所有的单独页面选项都将无效】',
            't_inside' => '泛内页',
            't_detail' => '详情页',
            't_tags' => '标签页',
            't_customize' => '自定义页面',
            't_list' => '列表页',
            't_common' => '公共页面页',
            'type' => '网页类型',
            'cate' => '类型',
            'status' => '状态',
            'user_id' => '创建者',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public static function getTmpType($key = 'all')
    {
        $data = [
            Template::TYPE_HOME => 't_home',
            Template::TYPE_LIST => 't_list',
            Template::TYPE_DETAIL => 't_detail',
            Template::TYPE_TAGS => 't_tags',
            Template::TYPE_CUSTOMIZE => 't_customize',
            Template::TYPE_COMMON => 't_common',
            Template::TYPE_INSIDE => 't_inside',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 按照域名生成模板 初始化 */
    public static function setTmp($domainId = 0, $tmpId = 0)
    {
//        list($domainId, $topDomain) = Domain::getDomainId();

        //模组更新
        $topDomain = Domain::findOne($domainId)->name;
        //获取域名id 查询对应的分类以及模板
        if ($domainId) {
            //查询所有的分类
            foreach (DomainColumn::getColumn($domainId) as $column) {
                $tpls = self::find()->where([
                    'domain_id' => $domainId,
                    'column_id' => $column['id'],
                ])->all();
                foreach ($tpls as $tpl) {
                    self::saveTemp($tpl, $topDomain, $column['name']);
                }
            }
        }

        //模板更新
        if ($tmpId) {
            $type = Template::findOne($tmpId)->type;
            $typeField = self::getTmpType($type);
            //查询所有包含该模板的栏目
            $column = DomainTpl::find()->select('column_id')->where([$typeField => $tmpId])->distinct('column_id')->asArray()->all();
            $columnArr = [];
            foreach ($column as $item) {
                $col = DomainColumn::findOne($item['column_id']);
                $columnArr[] = [
                    'domain' => $col->domain->name,
                    'domain_id' => $col->domain_id,
                    'id' => $item['column_id'],
                    'name' => $col->name
                ];
            }

            foreach ($columnArr as $column) {
                $tpls = self::find()->where([
                    'domain_id' => $column['domain_id'],
                    'column_id' => $column['id'],
                ])->all();
                foreach ($tpls as $tpl) {
                    self::saveTemp($tpl, $column['domain'], $column['name']);
                }
            }
        }
    }


    /** 设置模板 */
    public static function saveTemp($tpl, $topDomain, $columnName)
    {
        $tmpHome = Template::findOne($tpl->t_home);
        $tmpList = Template::findOne($tpl->t_list);
        $tmpDetail = Template::findOne($tpl->t_detail);
        $tmpTags = Template::findOne($tpl->t_tags);
        $tmpCommon = Template::findOne($tpl->t_common);
        $tmpInside = Template::findOne($tpl->t_inside);

        $tmpCustomize = Template::find()->where(['in', 'id', explode(',', $tpl->t_customize)])->all();

        if ($columnName == 'home') {
            if ($tpl->cate == Template::CATE_PC) {
                $path = __DIR__ . '/../../frontend/views/site/' . $topDomain . '/' . $columnName . '/static/';
            } else {
                $path = __DIR__ . '/../../frontend/views/site/' . $topDomain . '/' . $columnName . '/m_static/';
            }
        } else {
            if ($tpl->cate == Template::CATE_PC) {
                $path = __DIR__ . '/../../frontend/views/fan/' . $topDomain . '/' . $columnName . '/static/';
            } else {
                $path = __DIR__ . '/../../frontend/views/fan/' . $topDomain . '/' . $columnName . '/m_static/';
            }
        }

        FileHelper::createDirectory($path);             // 创建目录

        //主要页面
        foreach (Template::getTmpIndex() as $key => $item) {
            switch ($key) {
                case Template::TYPE_HOME:
                    $tmp = $tmpHome;
                    break;
                case Template::TYPE_LIST:
                    $tmp = $tmpList;
                    break;
                case Template::TYPE_DETAIL:
                    $tmp = $tmpDetail;
                    break;
                case Template::TYPE_TAGS:
                    $tmp = $tmpTags;
                    break;
                case Template::TYPE_INSIDE:
                    $tmp = $tmpInside;
                    break;
            }

            if (!empty($tmpHome)) {
                file_put_contents($path . $item, $tmp->content);
            }
        }

        //自定义页面处理
        foreach ($tmpCustomize as $value) {
            file_put_contents($path . $value->en_name . '.php', $value->content);
        }

        $layoutPath = __DIR__ . '/../../frontend/views/layouts/' . $topDomain . '/' . $columnName . '/';

        FileHelper::createDirectory($layoutPath);                 // 创建目录

        //公共页面处理
        if ($tmpCommon) {
         
            if ($tpl->cate == Template::CATE_PC) {
                file_put_contents($layoutPath . 'main.php', $tmpCommon->content);
            } else {
                file_put_contents($layoutPath . 'm_main.php', $tmpCommon->content);
            }
        }
    }

    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /** 获取类目 */
    public function getColumn()
    {
        return $this->hasOne(DomainColumn::className(), ['id' => 'column_id']);
    }

    /** 获取类目 */
    public function getTemplateTpl()
    {
        return $this->hasOne(TemplateTpl::className(), ['id' => 'tpl_id']);
    }

    /** 更换套装之后的 信息替换 */
    public function changeTpl($model, $tplId)
    {
        //查询出套装里面的信息
        $tpl = TemplateTpl::findOne($tplId);
        if ($tpl) {
            $model->t_customize = $tpl->t_customize;
            $model->t_tags = $tpl->t_tags;
            $model->t_detail = $tpl->t_detail;
            $model->t_list = $tpl->t_list;
            $model->t_common = $tpl->t_common;
            $model->t_home = $tpl->t_home;
            $model->t_inside = $tpl->t_inside;
        } else {
            return [-1, '没有该模组信息！'];
        }
        return [1, $model];
    }


}
