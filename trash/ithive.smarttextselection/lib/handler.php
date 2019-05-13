<?php
namespace ITHive\SmartTextSelection;

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Handler
{
    public static function onEpilog()
    {
        global $USER;
		
		$module_id = GetModuleID(__FILE__);
		$moduleStatus = Loader::includeSharewareModule($module_id);
		if($moduleStatus == Loader::MODULE_NOT_FOUND || $moduleStatus == Loader::MODULE_DEMO_EXPIRED)
			return;
		
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if ($GLOBALS["USER"]->GetID() == 1)
            file_put_contents($_SERVER['DOCUMENT_ROOT']."/logSMTS.txt", "REQ: ".var_export($request->getCookieRaw('MOBILE_APP_VERSION'), true).PHP_EOL, FILE_APPEND);
        if($request->isAdminSection() || $request->isAjaxRequest() || !$USER->IsAuthorized() || !empty($request->getCookieRaw('MOBILE_APP_VERSION')) || !empty($request->getCookie('MOBILE_APP_VERSION')) || \CSite::InDir('/bitrix/'))
            return;

        \CJSCore::Init(['jquery', 'popup', 'core', 'fastobject_core']);

        ?>
        <div class="smart-text-selection-menu">
            <span class="smart-text-selection-menu-item task" data-action="task" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_TASK')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_TASK')?></span>
            <span class="smart-text-selection-menu-item mail" data-action="mail" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_MAIL')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_MAIL')?></span>
            <span class="smart-text-selection-menu-item event" data-action="event" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_EVENT')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_EVENT')?></span>
            <span class="smart-text-selection-menu-item chat" data-action="chat" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_CHAT')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_CHAT')?></span>
            <span class="smart-text-selection-menu-item livefeed" data-action="livefeed" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_LIVEFEED')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_LIVEFEED')?></span>
            <?if (\Bitrix\Main\Loader::includeModule('ithive.iboard')) {?>
                <span class="smart-text-selection-menu-item idea-icon" data-action="idea" title="<?=Loc::getMessage('FASTOBJECT_FASTMENU_IDEA')?>"><?=Loc::getMessage('FASTOBJECT_FASTMENU_IDEA')?></span>
            <?}?>
        </div>
        <?
    }
}
?>