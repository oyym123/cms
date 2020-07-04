<?php


namespace common\models;

use Yii;

class DirCatch extends \yii\db\ActiveRecord
{
    /** 抓取所有的文章 */
    public function catchArticle()
    {
//        $data = $this->findDir('G:\每日文章\0伪原创\曹华强伪原创\陈冬媚 8.22 曾俊伟关键词');
//        echo '<pre>';
//        print_r($data);
//        exit;

        $dirPath = 'G:\每日文章\0外包软文\8.20 第三方 文章\已分发 2队\【8、26】G190817-4 weiSEO一 6-10\G190817-4 weiSEO一 6-10';
        $res = str_replace('\\', '/', $dirPath);
        $data = $this->getAllFileName($res);//调用函数，遍历C盘下的wampserver安装目录
        $resArr = $error = [];

        //抓取数据到数据库中
        foreach ($data as $key => $item) {
            $arr = explode('/', $item);
            $str = end($arr);
            $title = substr($str, 0, strrpos($str, "."));
            $content = iconv('gbk', 'utf-8', file_get_contents($item));
            $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
            $str_encode = mb_convert_encoding($str, 'UTF-8', $encode);
            $keywords = '';
            //截取前面10个字符
            $title = mb_substr($title, 0, 10);

            $title = $this->cleanNum($title);

            print_r($item);
            exit;
            $content1 = mb_substr($content, 10);
            $content2 = mb_substr($content, 0, 10);
            $content2 = $this->cleanNum($content);
            $content = $content1 . $content2;

            //按行分句
            if (stripos($content, '关键词') !== false) {
                preg_match('@关键词(.*?)
@', $content, $result);

                $keywords = $result[1];
                $keywords = str_replace(['：', ':'], ['', ''], $keywords);
                $content = str_replace($result[0], '', $content);
            }
            $partContent = array_filter(explode('
', $content));
            $a = [];

            foreach ($partContent as $value) {
                if ($value != ' ' && !empty($value)) {
                    $a[] = mb_convert_encoding($value, 'utf-8');
                }
            }

            if (empty($content)) {
                $error[] = '文章内容为空!';
                continue;
            }

            $part = json_encode($a, JSON_UNESCAPED_UNICODE);

            $dataSave = [
                'title' => $title,
                'type' => WhiteArticle::TYPE_DOC,
                'key_id' => 0,
                'keywords' => $keywords,
                'cut_word' => '',
                'image_urls' => '',
                'from_path' => $item,
                'word_count' => '',
                'part_content' => $part,
                'content' => $content,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $resArr[] = $dataSave;

            list($code, $msg) = WhiteArticle::createOne($dataSave);

            if ($code < 0) {
                $error[] = $msg;
            }
        }

        echo '<pre>';
        print_r($error);
        exit;
    }


    /** 去除开头的数字序号 */
    public function cleanNum($content)
    {
        $content = preg_replace('@\d(.*)?\\.@', '', $content);
        $content = preg_replace('@\d(.*)?、@', '', $content);
        $num = intval($content);
        $content = str_replace($num, '', $content);
        return $content;
    }

    /** 递归获取目录下所有的文件名称  */
    public function getAllFileName($directory, &$fileArr = [])
    {

        $mydir = dir($directory);
//        echo "<ul>\n";
        while ($file = $mydir->read()) {
            if ((is_dir("$directory/$file")) and ($file != ".") and ($file != "..")) {
//                echo "<li><font color=\"#ff00cc\"><b>$file</b></font></li>\n";
                $this->getAllFileName("$directory/$file", $fileArr);
            } else if (($file != ".") and ($file != "..")) {
                $fileArr[] = $directory . '/' . $file;
//                echo "<li>$directory/$file</li>\n";
            }
        }

//        echo "</ul>\n";
        $mydir->close();
        return $fileArr;
    }

}