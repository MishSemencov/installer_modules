<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use Bitrix\Main\Localization\Loc;
$reminderExtensions = array("jquery", "socnetlogdest");
CJSCore::Init($reminderExtensions);
?>
<div id="idea-watchers-wrapper">
    <span class="feed-add-post-form-but feed-add-mention idea-add-watcher" id="idea-add-watcher" title="<?=Loc::GetMessage("MEMBER_TITLE")?>"></span>
    <div id="idea-watchers-popup" class="idea-popup">
        <?
        $APPLICATION->IncludeComponent(
            'bitrix:tasks.widget.member.selector',
            '',
            array(
                'TEMPLATE_CONTROLLER_ID' => 'idea-watchers',
                'DISPLAY' => 'inline',
                'MIN' => 1,
                'TYPES' => array('USER', 'USER.EXTRANET', 'USER.MAIL'),
                'INPUT_PREFIX' => 'IDEA_WATCHERS',
                'ATTRIBUTE_PASS' => array(
                    'ID',
                    'NAME',
                    'LAST_NAME',
                    'EMAIL',
                ),
                'DATA' => array(),
                'READ_ONLY' => 'N',
            ),
            false,
            array("HIDE_ICONS" => "Y", "ACTIVE_COMPONENT" => "Y")
        )
        ?>
        <i class="idea-popup-close popup-window-close-icon"></i>
    </div>
</div>
