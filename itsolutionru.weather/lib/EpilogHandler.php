<?php
use Bitrix\Main\Page\Asset;

class EpilogHandler
{

    const module_id = 'itsolutionru.weather';

    public function onEpilog()
    {
        self::show();
    }

    private static function show()
    {
        \CJSCore::Init(['jquery']);
        Asset::getInstance()->addCss('/local/modules/'.self::module_id.'/css/main.css');
        Asset::getInstance()->addJs('/local/modules/'.self::module_id.'/js/main.js');
    }
}