<?php
namespace ITHive\Tools;

use Bitrix\Main\Page\Asset;

class EpilogHandler
{

    const module_id = 'ithive.tools';

    public function onEpilog()
    {
        self::supportBtn();
    }

    private static function supportBtn()
    {
        \CJSCore::Init(['jquery']);
        Asset::getInstance()->addCss('/local/modules/'.self::module_id.'/css/tools.css');
        Asset::getInstance()->addJs('/local/modules/'.self::module_id.'/js/support_btn.js');
    }
}