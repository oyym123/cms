<?php


namespace common\models;

use Yii;

class DirCatch extends \yii\db\ActiveRecord
{
    /** 抓取所有的文章 .txt格式 */
    public function catchArticle()
    {
//        $data = $this->findDir('G:\每日文章\0伪原创\曹华强伪原创\陈冬媚 8.22 曾俊伟关键词');
//        echo '<pre>';
//        print_r($data);
//        exit;
        set_time_limit(0);

        $dirPath = 'G:\每日文章\0外包软文';
        $res = str_replace('\\', '/', $dirPath);
        $data = $this->getAllFileName($res);//调用函数，遍历
        $resArr = $error = [];

        //抓取数据到数据库中
        foreach ($data as $key => $item) {
            if (strpos($item, '.txt') === false) {
                $error[] = '没有txt文件';
                continue;
            }
            $arr = explode('/', $item);
            $str = end($arr);
            $title = substr($str, 0, strrpos($str, "."));
            $content = file_get_contents($item);
            $encode = mb_detect_encoding($content, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));

            try {
                $content = mb_convert_encoding($content, 'UTF-8', $encode);
            } catch (\Exception $e) {

                $content = iconv('gbk', 'UTF-8', $content);
            }

            $keywords = '';

            //截取前面5个字符  目的是将数字去掉
            $title1 = mb_substr($title, 6);
            $subTitle = mb_substr($title, 0, 6);
            $title2 = $this->cleanNum($subTitle);
            $title = $title2 . $title1;

            $content1 = mb_substr($content, 5);
            $content2 = $this->cleanNum(mb_substr($content, 0, 5));
            $content = $content2 . $content1;

            //按行分句 后面可以随机重组
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
                'type' => WhiteArticle::TYPE_DOC_TXT,
                'key_id' => 0,
                'keywords' => $keywords,
                'cut_word' => '',
                'image_urls' => '',
                'from_path' => $item,
                'word_count' => mb_strlen($content),
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

    /** 抓取html */
    public function catchHtmlArticle()
    {
        set_time_limit(0);
        $dirPath = 'D:\文章作业';
        $res = str_replace('\\', '/', $dirPath);
        $data = $this->getAllFileName($res);//调用函数，遍历
        $resArr = $error = [];
//        $data = array_slice($data, 0, 1000);

        //抓取数据到数据库中
        foreach ($data as $key => $item) {
            if (strpos($item, '.html') === false) {
//                $error[] = '不是html文件';
                continue;
            }

            $arr = explode('/', $item);
            $str = end($arr);
            //标题类处理
            $title = substr($str, 0, strrpos($str, "."));
            $title = $this->cleanHtmlTitle($title);

            if (strpos($title, '副本') !== false) {
                $error[] = ['含有副本，不可保存！'];
                continue;
            }

            //截取前面6个字符  如果其中包含 . 则将数字去掉
            $subTitle = mb_substr($title, 0, 6);
            if (strpos($subTitle, '.') !== false) {
                $title1 = mb_substr($title, 6);
                $title2 = $this->cleanNum($subTitle);
                $title = $title2 . $title1;
            }

//            $item = 'D:/文章作业/2017年全年雅思IELTS真题打包下载.html';
            $content = file_get_contents($item);
//            print_r($title);
//            exit;


            //获取纯文本
            $contentTxt = Tools::cleanHtml($content);
            $partContent = array_filter(explode('
', $contentTxt));

            //除掉杂乱标签
            $content = str_replace('&#xa0;', '', $content);

            $a = [];
            foreach ($partContent as $value) {
                if ($value != ' ' && !empty($value)) {
                    $a[] = mb_convert_encoding($value, 'utf-8');
                }
            }

            $part = json_encode($a, JSON_UNESCAPED_UNICODE);

            //图片获取url
            preg_match('@<img src="(.*)?" width@', $content, $imgData);
            $imgData = array_filter(explode('<img src="', $imgData[0]));
            $imgArr = [];
            foreach ($imgData as $imgDatum) {
                $imgArr[] = preg_replace('@" width(.*)?@', '', $imgDatum);
            }

            $dataSave = [
                'title' => $title,
                'type' => WhiteArticle::TYPE_DOC_WORD,
                'key_id' => 0,
                'keywords' => '',
                'cut_word' => '',
                'image_urls' => $imgArr ? json_encode($imgArr) : '',
                'from_path' => $item,
                'word_count' => mb_strlen($contentTxt),
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
        //去除空格
        $content = str_replace([' ', '、', ',', '，', '.', '。'], '', $content);
        $content = str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], '', $content);
        return $content;
    }

    /** 清除html标题 */
    public function cleanHtmlTitle($title)
    {
        $title = preg_replace('@（(.*)?）@', '', $title);
        $title = preg_replace('@\((.*)?\)@', '', $title);
        return $title;
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