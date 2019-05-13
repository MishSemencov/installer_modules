<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="offset-bottom">
    <b><?=Loc::Getmessage("CREATE_FROM_IDEA");?></b>
</div>
<div>
    <a class="idea-btn idea-btn-simple idea-btn-success idea-task-create">
        <i class="icon icon-list btn-icon"></i> <?=Loc::Getmessage("CREATE_TASK");?>
    </a>
    <a class="idea-btn idea-btn-simple idea-btn-success idea-chat-create">
        <i class="icon icon-chat btn-icon"></i> <?=Loc::Getmessage("CREATE_CHAT_MESSAGE");?>
    </a>
    <a class="idea-btn idea-btn-simple idea-btn-success idea-event-create">
        <i class="icon icon-calendar2 btn-icon"></i> <?=Loc::Getmessage("CREATE_MEETING");?>
    </a>
    <a class="idea-btn idea-btn-simple idea-btn-success idea-live-feed-create">
        <i class="icon icon-blog btn-icon"></i> <?=Loc::Getmessage("CREATE_LIVE_FEED");?>
    </a>
    <a class="idea-btn idea-btn-simple idea-btn-success idea-mail-create">
        <i class="icon icon-mail btn-icon"></i> <?=Loc::Getmessage("CREATE_LETTER");?>
    </a>
</div>

<hr>