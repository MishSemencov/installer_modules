<?php
namespace ITHive\SJTickets;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class SJRequest
{

    private static $_instance = null;
    private $httpClient;
    private $postUrl;
    public $result;
    public $postData;
    const ticketPostUrl = 'https://www.softjoys.pro/api/v1/?obj=task&act=add&key=a5cd343c74733353edba977d4c2dc5da';
    const commentPostUrl = 'https://www.softjoys.pro/api/v1/?obj=comment&act=addtotask&key=a5cd343c74733353edba977d4c2dc5da';
    const ticketCloseUrl = 'https://www.softjoys.pro/api/v1/?obj=task&act=close&key=a5cd343c74733353edba977d4c2dc5da';


    private function __construct()
    {
        $this->httpClient = new HttpClient();
        $this->httpClient->setHeader('Content-Type', 'application/json', true);
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    static public function getInstance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function postTicket($data)
    {
        $this->postData = $data;
        $this->postUrl = self::ticketPostUrl;
        $this->request();
    }

    public function closeTicket($data)
    {
        $this->postData = $data;
        $this->postUrl = self::ticketCloseUrl;
        $this->request();
    }

    public function postComment($data)
    {
        $this->postData = $data;
        $this->postUrl = self::commentPostUrl;
        $this->request();
    }

    protected function request()
    {
        $this->httpClient->setHeader('Content-Length', strlen(json_encode($this->postData)));
        $result = $this->httpClient->post($this->postUrl, json_encode($this->postData));
        $arLog = [
            'date' => date('Y.m.d H:i'),
            'url' => $this->postUrl,
            'data' => json_encode($this->postData),
            'result' => $result,
            'errors' => $this->httpClient->getError()
        ];
        Debug::dumpToFile($arLog, 'Request data: ', 'sjtickets_request.txt');

        //test

        //$this->curlRequest('https://portal.it-hive.ru/sj_test.php', json_encode($this->postData));
        //$this->curlRequest('http://testportal.wehive.digital/sj_test.php', json_encode($this->postData));

        try
        {
            $result = str_replace("\xEF\xBB\xBF", '', $result);
            $ar = Json::decode($result);
            if ($ar['status'] === 'error'){
                Debug::dumpToFile($arLog, 'Error for request: ', 'sjtickets_request_errors.txt');
            }
        }
        catch(ArgumentException $e)
        {
            //todo
        }
    }
    protected function curlRequest($url = null, $data)
    {
        if (!empty($this->postData)) {
            if (function_exists('curl_init') && $curl = curl_init()) {

                if(!empty($url))
                    $curlUrl = $url;
                elseif(!empty($this->postUrl))
                    $curlUrl = $this->postUrl;
                else
                    return false;

                if(!empty($data))
                    $curlData = $data;
                elseif(!empty($this->postData))
                    $curlData = $this->postData;
                else
                    return false;

                curl_setopt($curl, CURLOPT_URL, $curlUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curlData);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($curlData))
                );
                $result = curl_exec($curl);
                curl_close($curl);
                return $result;
            }
        }
    }
}