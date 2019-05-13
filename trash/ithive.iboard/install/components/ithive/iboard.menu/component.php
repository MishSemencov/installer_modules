<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($this->StartResultCache(false, [])) {
	
	if(!empty($arParams['URL_TEMPLATES'])){
		foreach($arParams['URL_TEMPLATES']['LINKS'] as $code => $url){
		    if (GetMessage('ITHIVE_IBOARD_MENU_ITEM_'.$code))
                $arResult['ITEMS'][] = [
                    'TITLE' => GetMessage('ITHIVE_IBOARD_MENU_ITEM_'.$code),
                    'URL' => $arParams['URL_TEMPLATES']['BASE_FOLDER'].$url
                ];
		}
	}
	$this->IncludeComponentTemplate();
}