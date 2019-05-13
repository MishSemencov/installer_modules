<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.buttons");
\Bitrix\Main\UI\Extension::load("ui.buttons.icons");

Loc::loadMessages(__FILE__);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var CBitrixComponent $component */

$helper = $arResult['HELPER'];

$taskId = $arParams["TASK_ID"];
$can = $arParams["TASK"]["ACTION"];
$taskData = $arParams["TASK"];

if (\Bitrix\Main\ModuleManager::isModuleInstalled('rest'))
{
    $APPLICATION->IncludeComponent(
        'bitrix:app.placement',
        'menu',
        array(
            'PLACEMENT'         => "TASK_LIST_CONTEXT_MENU",
            "PLACEMENT_OPTIONS" => array(),
            //			'INTERFACE_EVENT' => 'onCrmLeadListInterfaceInit',
            'MENU_EVENT_MODULE' => 'tasks',
            'MENU_EVENT'        => 'onTasksBuildContextMenu',
        ),
        null,
        array('HIDE_ICONS' => 'Y')
    );
}


?>
<style>
    #modal_form {
        width: 450px;
        height: 115px;
        border-radius: 10px;
        background: #fff;
        position: fixed;
        top: 55%;
        left: 60%;
        margin-top: -200px;
        margin-left: -350px;
        display: none;
        z-index: 4000;
        padding: 20px 10px;
    }
    #overlay {
        z-index:3000;
        position:fixed;
        background-color:#000;
        opacity:0.8;
        -moz-opacity:0.8;
        filter:alpha(opacity=80);
        width:100%;
        height:100%;
        top:0;
        left:0;
        cursor:pointer;
        display:none;
    }
</style>
<div id="<?=$helper->getScopeId()?>" class="task-view-buttonset <?=implode(' ', $arResult['CLASSES'])?>">

	<span data-bx-id="task-view-b-timer" class="task-timeman-link">
		<span class="task-timeman-icon"></span>
		<span id="task_details_buttons_timer_<?=$taskId?>_text" class="task-timeman-text">

		<span data-bx-id="task-view-b-time-elapsed"><?=\Bitrix\Tasks\UI::formatTimeAmount($taskData['TIME_ELAPSED']);?></span>

            <?if ($taskData["TIME_ESTIMATE"] > 0):?>
                / <?=\Bitrix\Tasks\UI::formatTimeAmount($taskData["TIME_ESTIMATE"]);?>
            <?endif?>
		</span>
		<span class="task-timeman-arrow"></span>
	</span>

    <span data-bx-id="task-view-b-buttonset">

		<span data-bx-id="task-view-b-button" data-action="START_TIMER" class="task-view-button timer-start ui-btn ui-btn-success">
			<?=Loc::getMessage("TASKS_START_TASK_TIMER")?>
		</span>

		<span data-bx-id="task-view-b-button" data-action="PAUSE_TIMER" class="task-view-button timer-pause ui-btn ui-btn-light-border">
			<?=Loc::getMessage("TASKS_PAUSE_TASK_TIMER")?>
		</span>

		<span data-bx-id="task-view-b-button" data-action="START" class="task-view-button start ui-btn ui-btn-success">
			<?=Loc::getMessage("TASKS_START_TASK")?>
		</span>

		<span data-bx-id="task-view-b-button" data-action="PAUSE" class="task-view-button pause ui-btn ui-btn-success">
			<?=Loc::getMessage("TASKS_PAUSE_TASK")?>
		</span>

		<span data-bx-id="task-view-b-button" data-action="COMPLETE"  class="task-view-button complete pause ui-btn ui-btn-success">
			<?=Loc::getMessage("TASKS_CLOSE_TASK")?>
		</span>
        <?php if($arParams['TASK']['REAL_STATUS'] != 6):?>
            <span data-bx-id="task-view-b-button" class="task-view-button complete pause ui-btn ui-btn-primary" onclick="move();" >
                <?="Отложить на"?>
            </span>
        <?endif;?>
        <span data-bx-id="task-view-b-button" data-action="APPROVE"  class="task-view-button approve ui-btn ui-btn-success">
			<?=Loc::getMessage("TASKS_APPROVE_TASK")?>
		</span>

		<span data-bx-id="task-view-b-button" data-action="DISAPPROVE" class="task-view-button disapprove ui-btn ui-btn-danger">
			<?=Loc::getMessage("TASKS_REDO_TASK")?>
		</span>

		<span data-bx-id="task-view-b-open-menu" class="task-more-button ui-btn ui-btn-light-border ui-btn-dropdown">
			<?=Loc::getMessage("TASKS_MORE")?>
		</span>

		<a href="<?=$arResult['EDIT_URL']?>" class="task-view-button edit ui-btn ui-btn-link" data-slider-ignore-autobinding="true">
			<?=GetMessage("TASKS_EDIT_TASK")?>
		</a>

		<script type="text/html" data-bx-id="task-view-b-timeman-confirm-title">
			<span><?=Loc::getMessage('TASKS_TASK_CONFIRM_START_TIMER_TITLE');?></span>
		</script>
		<script type="text/html" data-bx-id="task-view-b-timeman-confirm-body">
			<div style="width: 400px; padding: 25px;"><?=Loc::getMessage('TASKS_TASK_CONFIRM_START_TIMER');?></div>
		</script>

	</span>
</div>
<div id="modal_form">
    <div style="text-align:center; margin-top:-20px;font-family: OpenSans-Bold;">Выберите срок:</div>
    <div style="font-family: OpenSans;margin-top: -10px;text-align:center;">
        <input type="number" id="days" style="width:50px;"> дней, <input type="number" id="hours" style="width:50px;"> часов : <input type="number" id="minutes" style="width:50px;"> минут
    </div>
    <div style="text-align:center;">
        <button class="complete pause ui-btn ui-btn-primary" onclick="submit();" id="submitButton">Отложить</button>
    </div>
</div>
<div id="overlay" onclick="closePopup();"></div>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
    var taskID = <?=$arParams['TASK_ID']?>;
    var url = '<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")?>://<?php echo $_SERVER['HTTP_HOST'];?>/local/deferIts/itsapi.php';
    function closePopup()
    {
        document.getElementById("modal_form").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }
    function move()
    {
        document.getElementById("modal_form").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }
    function submit()
    {
        let minutes = document.getElementById("minutes").value == "" ? 0 : document.getElementById("minutes").value;
        let hours = document.getElementById("hours").value == "" ? 0 : document.getElementById("hours").value;
        let days = document.getElementById("days").value == "" ? 0 : document.getElementById("days").value;
        if(minutes < 0 || hours < 0 || days < 0) return alert("Отрицательные числа недопустимы.");
        if(hours == 0 && days == 0 && minutes < 15) return alert("Невозможно отложить задачу меньше, чем на 15 минут.");
        if(hours > 23) return alert("Нельзя установить более 23-х часов.");
        if(minutes > 59) return alert("Нельзя установить более 59-и минут.");
        let time = minutes*60 + hours*60*60 + days*24*60*60;
        $.ajax({
            type: "GET",
            url: url,
            data: "action=defer&taskID=" + taskID + "&time=" + time,
            beforeSend: function()
            {
                $("#submitButton").attr('disabled', true);
                $("#submitButton").html("Подождите...");
            },
            success: function (response) {
                alert("Успешно перенесено!");
                location.reload();
            }
        });
    }
    BX.message({
        TASKS_REST_BUTTON_TITLE: '<?=Loc::getMessage('TASKS_REST_BUTTON_TITLE')?>',
        TASKS_DELETE_SUCCESS: '<?=GetMessage('TASKS_DELETE_SUCCESS')?>'
    });
</script>
<?$helper->initializeExtension();?>