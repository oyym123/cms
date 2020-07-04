<?php

namespace common\models;

require_once \Yii::$app->basePath . '/../common/widgets/drapisdk_php/common.php';
require_once \Yii::$app->basePath . '/../common/widgets/drapisdk_php/sms_service_KRService.php';

class BaiDuSdk extends sms_service_KRService
{

    public $aFileId = "eaa0c088c72fa28d505636f6b3dae276";
    public $planId = 27850776;

    public function getKRByQueryTest($keywords)
    {
        $request = new GetKRByQueryRequest();
        $request->setQueryType(1);
        $request->setQuery($keywords);
        $this->setIsJson(true);
        $response = $this->getKRByQuery($request);
        $head = $this->getJsonHeader();

//        echo "status getKRByQuery:" . json_encode($head) . "\n";
//        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }

    public function getKRCustomTest()
    {
        $request = new GetKRCustomRequest();
        $request->setId($this->planId);
        $request->setIdType(3);
        $response = $this->getKRCustom($request);
        $head = $this->getJsonHeader();
        echo "status getKRCustom:" . json_encode($head) . "\n";
        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }

    public function getEstimatedDataByBidTest($keywords)
    {
        $request = new GetEstimatedDataByBidRequest();
        $kret = new KREstimatedType();
        $kret->setBid(5);
        $kret->setWord($keywords);
        $request->setWords(array($kret));
        $response = $this->getEstimatedDataByBid($request);
        $head = $this->getJsonHeader();
        echo "status getEstimatedDataByBid:" . json_encode($head) . "\n";
//        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }

    public function getEstimatedDataTest($keywords)
    {
        $request = new GetEstimatedDataRequest();
        $kret = new KREstimatedType();
        $kret->setWord($keywords);
        $request->setWords(array($kret));
        $response = $this->getEstimatedData($request);
        $head = $this->getJsonHeader();
        echo "status getEstimatedData:" . json_encode($head) . "\n";
        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }

    public function getKRFileIdByWordsTest()
    {
        $request = new GetKRFileIdByWordsRequest();
        $request->setSeedWords(array("鲜花"));
        $response = $this->getKRFileIdByWords($request);
        $head = $this->getJsonHeader();
        echo "status getKRFileIdByWords:" . json_encode($head) . "\n";
        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }

    public function getFilePathTest()
    {
        $request = new GetKRFileRequestParams();
        $request->setFileId($this->aFileId);
        $response = $this->getFilePath($request);
        $head = $this->getJsonHeader();
        echo "status getFilePath:" . json_encode($head) . "\n";
        assert(SUCCESS == $head->desc && 0 == $head->status);
        return $response->data;
    }


    public function getFileStatusTest()
    {
        $request = new GetFileStatusRequest();
        $request->setFileId($this->aFileId);
        $response = $this->getFileStatus($request);
        $head = $this->getJsonHeader();
        echo "status getFileStatus:" . json_encode($head) . "\n";
        assert(SUCCESS == $head->desc && 0 == $head->status);
        //$this->aFileId = $response->data[0]->fileId;
        return $response->data;
    }

    /**
     *  获取相关 关键词 300个
     */
    public function getKeyWords($keywords)
    {
        $datas = $this->getKRByQueryTest($keywords);
        $arr = json_decode(json_encode($datas), TRUE);
        return $arr;
    }

    /**
     * 获取关键词的竞争排名
     */
    public function getRank()
    {
        $datas = $this->getEstimatedDataByBidTest("外教");

        echo '<pre>';
        print_r($datas);exit;
        $arr = json_decode(json_encode($datas), TRUE);
        return $arr;
    }

}
