<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(_FILE_);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($request->isAjaxRequest()) {

    $requiredModules = array('main');

    foreach ($requiredModules as $requiredModule) {
        if (!CModule::IncludeModule($requiredModule)) {
            ShowError(GetMessage("F_NO_MODULE"));
            return 0;
        }
    }
    $request->getPost('action');

    if($request->getPost('action')){
        /*get idea array*/
        $ideaId = $request->getPost('ideaId');
        if ((intval($ideaId) > 0 || is_array($ideaId) && count($ideaId) > 0) && \Bitrix\Main\Loader::includeModule('ithive.iboard')) {
            $arOriginIdeasId = array();
            $arIdeaImages = array();
            $arIdeaText = array();

            $arIdeas = \ITHive\IBoard\Ideas::getList(array("id" => $ideaId), array("id", "origin_id", "name", "description", "important"));

            foreach ($arIdeas as $curIdeaId => $arIdea) {
                if (!in_array($arIdea["ORIGIN_ID"], $arOriginIdeasId) && intval($arIdea["ORIGIN_ID"]))
                    $arOriginIdeasId[$curIdeaId] = $arIdea["ORIGIN_ID"];
                if (strlen(trim($arIdea["DESCRIPTION"])))
                    $arIdeaText[] = Loc::Getmessage("HREF_TITLE") . $_SERVER["HTTP_ORIGIN"] . "/iboard/idea/" . $curIdeaId . "/\n" . trim($arIdea["NAME"]) . "\n" . trim($arIdea["DESCRIPTION"]) . "\n";
                if (strlen(trim($arIdea["NAME"])))
                    $ideaName = trim($arIdea["NAME"]);
                $ideaImportant = $arIdea["IMPORTANT"];
            }

            if (count($arOriginIdeasId) > 0) {
                $arOriginIdeas = \ITHive\IBoard\Ideas::getList(array("id" => $arOriginIdeasId), array("id", "name", "description"));
                foreach ($arOriginIdeas as $userIdeaId => $arOriginIdea) {
                    if (strlen(trim($arOriginIdea["DESCRIPTION"])))
                        $arIdeaText[] = Loc::Getmessage("HREF_TITLE") . $_SERVER["HTTP_ORIGIN"] . "/iboard/idea/" . $userIdeaId . "/\n" . trim($arOriginIdea["NAME"]) . "\n" . trim($arOriginIdea["DESCRIPTION"]) . "\n";
                    if (strlen(trim($arOriginIdea["NAME"])))
                        $ideaName = trim($arOriginIdea["NAME"]);
                }
            }
            $arIds = array_merge($arOriginIdeasId, array_keys($arIdeas));
            $arIdeaFiles = \ITHive\IBoard\IdeasFiles::getList($arIds);

            $ideaText = implode("\n", $arIdeaText);
            if (is_array($arIdeaFiles) && count($arIdeaFiles) > 0) {
                foreach ($arIdeaFiles as $arIdeasFiles) {
                    foreach ($arIdeasFiles["IMAGES"] as $arImage) {
                        $arIdeaImages[] = array(
                            "width" => $arImage["WIDTH"],
                            "height" => $arImage["HEIGHT"],
                            "src" => $arImage["SRC"],
                        );
                    }
                }
            }

            $data = [
                'action' => $request->getPost('action'),
                'text' => trim($ideaText),
                'name' => trim($ideaName),
                'important' => $ideaImportant,
                'images' => $arIdeaImages,
//                'pageUrl' => $request->getPost('pageUrl'),
                'mode' => 'iboard'
            ];

//            if ($request->getPost('pageUrl') != undefined)
//                $data['pageUrl'] = $request->getPost('pageUrl');
        } else {
            $name = ($request->getPost('name') != undefined) ? $request->getPost('name') : "";
            $ideaImportant = ($request->getPost('important') == 1) ? 1 : 0;
            $data = [
                'action' => $request->getPost('action'),
                'text' => $name . "\n" . $request->getPost('text'),
                'name' => $name,
                'important' => $request->getPost('important'),
                'images' => $request->getPost('images'),
            ];

            if ($request->getPost('pageUrl') != undefined && $request->getPost('pageUrl') != "")
                $data['pageUrl'] = $request->getPost('pageUrl');
        }
        $action = $request->getPost('action');
        global $APPLICATION;
        $APPLICATION->ShowAjaxHead(true, true, true, true);
        switch ($action){
            case 'task':
                include 'form_templates/task.php';
                break;
            case 'mail':
                include 'form_templates/mail.php';
                break;
            case 'event':
                include 'form_templates/event.php';
                break;
            case 'chat':
                include 'form_templates/chat.php';
                break;
            case 'livefeed':
                include 'form_templates/livefeed.php';
                break;
        }

        die();
    }






}

function showFormTpl($action, $data){

}