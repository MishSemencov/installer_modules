<?php
/**
 * Module handler class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
/**
 * Module and bitrix events handler class. Contain handler as methods.
 * @package ITHive\API
 */
class Handler {

    /**
     * This is sample handler for bitrix event OnBuildGlobalMenu.
     * It's add items into admin menu.
     * @param $aGlobalMenu
     * @param $arModuleMenu
     */
	public function OnBuildGlobalMenu(&$aGlobalMenu, &$arModuleMenu)
	{
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        if(\CMain::GetGroupRight('main') < 'R')
			return;

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @var \ithive_api $obModule */
        $obModule = \CModule::CreateModuleObject('ithive.api');

		$arMenu = array(
			'parent_menu' => 'global_menu_services', // #global_menu_*# (desktop, content, services, marketPlace, settings )
			'section' => 'ithive.api', // unique section id
			'sort' => 50,
			'text' => $obModule->MODULE_NAME, // it's main tab name and breadcrumbs title
			'title' => 'TEST_TITLE_ROOT_MENU_ITEM', // ???
			'icon' => 'xmpp_menu_icon', // standard bitrix icon_id
			'page_icon' => 'xmpp_page_icon', // standard bitrix icon_id, copy previous
			'items_id' => 'menu_ithive_api', // set item_id for js scripts work, it's REQUIRED for toggle menu
//			  'url' => '', // if it's not exist! (don't define this key) tab can toggle by click, or this is link
//			  'more_url' => array(),
			'items' => array(
			    array(
                    'text' => Loc::getMessage('ITHIVE_API_TEST_BOX'),
                    // if it's not exist! (don't define this key) tab can toggle by click, or this is link
//                    'url' => '/bitrix/admin/ithive_api_admin_page.php',
                    'title' => 'TEST_TITLE_DEPTH_ONE_MENU_ITEM',
//                    'icon' => '', // menu item icon
//                    'page_icon' => 'cloud_page_icon',
                    'items_id' => 'menu_ithive_api_mailing_list',
//                    'more_url' => array(),
//                    'dynamic' => true,
                    'items' => array(
                        array(
                            'text' => Loc::getMessage('ITHIVE_API_TEST_PAGE_A'),
                            'url' => '/bitrix/admin/ithive_api_admin_page.php',
                            'title' => 'TEST_TITLE_DEPTH_TWO_MENU_ITEM',
//                            'icon' => '', // menu item icon
//                            'page_icon' => 'cloud_page_icon',
                            'items_id' => 'menu_ithive_api_mailing_list2',
//                            'more_url' => array(),
//                            'dynamic' => true,
                            'items' => array()
                        )
                    )
                ),
                array(
                    'text' => Loc::getMessage('ITHIVE_API_TEST_PAGE_B'),
                    'url' => '/bitrix/admin/ithive_api_admin_page2.php',
                    'title' => 'TEST_TITLE_DEPTH_TWO_MENU_ITEM',
//                    'icon' => '', // menu item icon
//                    'page_icon' => 'cloud_page_icon',
//                    'items_id' => 'menu_ithive_api_mailing_list2',
//                    'more_url' => array(),
//                    'dynamic' => true,
                    'items' => array()
                )
            ),
		);

		$arModuleMenu[] = $arMenu;
	}
}
