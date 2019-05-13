<?php
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($request->isAjaxRequest()) {

    $module_id = GetModuleID(__FILE__);

    $requiredModules = array($module_id);

    foreach ($requiredModules as $requiredModule) {
        if (!CModule::IncludeModule($requiredModule)) {
            ShowError(GetMessage("F_NO_MODULE"));
            return 0;
        }
    }

    if($request->getPost('action')){

        $oAction = new \ITHive\SmartTextSelection\STSAction($request->getPost('action'), $request->getPostList()->toArray());

        if($oAction->status){
            $result = [
                'status' => 'success',
                'data' => 'Ok, lets rock!'
            ];
            if(!empty($oAction->resultData))
                $result['data'] = $oAction->resultData;

            echo json_encode($result);
            die();
        }else{
            if($oAction->getValidateErrors())
                $errors = $oAction->getValidateErrors();
            else
                $errors = 'some error in action';

            echo json_encode([
                'status' => 'error',
                'data' =>$errors
            ]);
            die();
        }
    }else{
        echo json_encode([
            'status' => 'error',
            'data' => 'no action parameter'
        ]);
        die();
    }



}