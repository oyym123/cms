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

        $dirPath = 'G:\每日文章\0外包软文\8.20 第三方 文章';
        $res = str_replace('\\', '/', $dirPath);
        $data = $this->getAllFileName($res);//调用函数，遍历C盘下的wampserver安装目录
        $resArr = $error = [];

        //抓取数据到数据库中
        foreach ($data as $key => $item) {
            $arr = explode('/', $item);
            $str = end($arr);
            $title = substr($str, 0, strrpos($str, "."));
            $content = iconv('gbk', 'utf-8', file_get_contents($item));
            $keywords = '';
            $title = preg_replace('@\d(.*)?\\.@', '', $title);
            $title = preg_replace('@\d(.*)?、@', '', $title);

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
                if ($value != ' ') {
                    $a[] = mb_convert_encoding($item, 'utf-8');
                }
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

    /** 将word文档转成html */
    public function word2html($wfilepath, $htmlname = '1.html')
    {
        $word = new \COM("word.application") or die("Unable to instanciate Word");
        $word->Visible = 1;
        $word->Documents->Open($wfilepath);
        $word->Documents[1]->SaveAs($htmlname, 8);
        $word->Quit();
        $word = null;
        unset($word);
    }

    /** 获取目录下所有的文件名称  */
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

    /**
     * @param $dir
     */
    public function findDir($dir)
    {
        $num = 0;    //用来记录目录下的文件个数
        $dirname = $dir; //要遍历的目录名字
        $dir_handle = opendir($dirname);

        echo '<table border="1" align="center" width="960px" cellspacing="0" cellpadding="0">';
        echo '<caption><h2>目录' . $dirname . '下面的内容</h2></caption>';
        echo '<tr align="left" bgcolor="#cccccc">';
        echo '<th>文件名</th><th>文件大小</th><th>文件类型</th><th>修改时间</th></tr>';
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                $dirFile = $dirname . "/" . $file;
                if ($num++ % 2 == 0)    //隔行换色
                    $bgcolor = "#ffffff";
                else
                    $bgcolor = "#cccccc";
                echo '<tr bgcolor=' . $bgcolor . '>';
                echo '<td>' . $file . '</td>';
                echo '<td>' . filesize($dirFile) . '</td>';
                echo '<td>' . filetype($dirFile) . '</td>';
                echo '<td>' . date("Y/n/t", filemtime($dirFile)) . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        closedir($dir_handle);
    }
}