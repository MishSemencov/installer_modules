<?php
/**
 * Module response class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API;

/**
 * Class Response
 * @package ITHive\API
 */
class Response
{
    /** @var array Assert formats */
    private $availableFormats = array('json');

    /**
     * Response constructor.
     * @param $data
     */
    public function __construct($data)
    {
        if(!empty($data)){
            foreach ($data as $name => $v) {
                $this->$name = $v;
            }
        }
    }

    /**
     * Show data in a specific format and return it
     * @param string $format
     */
    public function respond($format = 'json')
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        switch ($format) {
            case 'json' :
            default :
                header('Content-Type: application/json;  charset=utf-8;');
                $content = json_encode($this);
        }

        echo $content;
        Api::log($content, 'json respond');
        Api::log('========== END OF REQUEST ==========');
        die();
    }
}