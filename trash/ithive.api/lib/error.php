<?php
/**
 * Api base class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API;

/**
 * Api error (exceptions)
 * CAUTION! Error const values used at apidoc comments. After change something please replace it at all files manually.
 * @package ITHive\API
 */
class Error extends \Exception
{
    const API_ERR_UNKNOWN = 500;
    const API_ERR_APP = 1;
    const API_ERR_VERSION = 2;
    const API_ERR_ENTITY = 3;
    const API_ERR_ACTION = 4;
    const API_ERR_JSON = 5;
    const API_ERR_DEPRECATED = 6;
    const API_ERR_PAYSYSTEM = 7;

    const API_ERR_PARAM = 100;
    const API_ERR_PARAM_REQUIRED = 110;
    const API_ERR_PARAM_FORMAT_NUMBER = 120;
    const API_ERR_PARAM_FORMAT_ARRAY = 130;
    const API_ERR_PARAM_FORMAT_ARRAY_EMPTY = 131;
    const API_ERR_PARAM_FORMAT_ENTITY = 140;
    const API_ERR_PARAM_FORMAT_ARRAY_OF_ENTITY = 141;
    const API_ERR_PARAM_FORMAT_DATE = 150;
    const API_ERR_PARAM_FORMAT_BOOLEAN = 160;

    const API_ERR_FIELD_REQUIRED = 300;
    const API_ERR_PARAM_ACCESS_TOKEN = 320;
    const API_ERR_PARAM_REFRESH_TOKEN = 321;
    const API_ERR_PARAM_SESSION = 330;
    
    const API_ERR_MODULE_DEPENDENT = 700;

    /** @var \StdClass|null Object of error with required format (with code and message) */
    public $error;


    /**
     * Show error as json
     */
    public function showErrorXML()
    {
        $this->error = new \stdClass();
        $this->error->code = $this->getCode();
        $this->error->message = $this->getMessage();

        header('Content-Type: application/json;  charset=utf-8;');
        $content = json_encode($this);
        echo $content;
        Api::log($content, 'error');
        Api::log('========== END OF REQUEST ==========');
        die();
    }

}