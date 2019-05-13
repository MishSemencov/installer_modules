<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
CJSCore::Init(array("iboard_core"));
?>
<?if (!empty($arResult["IDEAS"])) {?>
    <div class="ideas-table-list ideas-scroller">
        <div class="idea-clear custom-drag"
             data-onstart="startCallback"
             data-ondrag="dragCallback"
             data-ondrop="dropCallback"
        >
            <?foreach ($arResult["IDEAS"] as $ideaId => $arIdea) {?>
                <div class="item">
                    <div class="idea<?=($arIdea["MIN_TABLE"] != 1) ? " active" : ""?>" style="border-color: #<?=$arResult["CATEGORIES"][$arIdea["CATEGORY_ID"]]["COLOR"]?>">
                        <input type="hidden" class="idea-id-input" name="IDEA_ID" value="<?=$ideaId?>" data-value="<?=$ideaId?>">
                        <div class="row collapsed-row head">
                            <div class="col-xs-8">
                                <a href="<?=$arIdea["DETAIL_PAGE_URL"]?>" class="title"><?=($arIdea["NAME"]) ? $arIdea["NAME"] : Loc::Getmessage("IDEA_NUMBER", array("#IDEA_ID#" => $ideaId))?></a>
                            </div>
                            <div class="col-xs-4 align-right">
                                <?if (is_array($arIdea["TASKS"])) {?>
                                    <span class="tooltip inline-middle">
                                        <span class="idea-check"></span>
                                        <span class="tooltip-text">
                                            <?=Loc::Getmessage("IDEA_TASKS_CREATED")?><br>
                                            <?foreach ($arIdea["TASKS"] as $arTask) {?>
                                                <a href="<?=$arTask["DATA"]?>"><?=$arTask["DATA"]?></a>
                                            <?}?>
                                        </span>
                                    </span>
                                <?}?>
                                <?if ($arIdea["IMPORTANT"] == 1) {?>
                                    <span data-tooltip="<?=Loc::Getmessage("IDEA_IMPORTANT")?>" class="inline-middle">
                                        <span class="idea-fire"></span>
                                    </span>
                                <?}?>
                                <input type="checkbox" class="inline-middle">
                                <button type="button" class="toggler idea-set-minimized" data-type="table">
                                    <i class="icon icon-menu<?=($arIdea["MIN_TABLE"] != 1) ? "-opened" : ""?>"></i>
                                    <i class="icon icon-menu<?=($arIdea["MIN_TABLE"] == 1) ? "-opened" : ""?>"></i>
                                </button>
                            </div>
                        </div>
                        <?if ($arIdea["DESCRIPTION"] != ""){?>
                            <div class="text">
                                <?=$arIdea["DESCRIPTION"]?>
                            </div>
                        <?}?>
                        <?if (is_array($arIdea["FILES"]["IMAGES"])) {?>
                            <ul class="idea-clear images-list idea-clearfix">
                                <?foreach ($arIdea["FILES"]["IMAGES"] as $arImg) {?>
                                    <li>
                                        <a href="<?=$arImg["SRC"]?>" data-lightbox="gallery-idea-<?=$ideaId?>">
                                            <figure class="image-cover"><img src="<?=$arImg["SRC"]?>" alt="<?=$arImg["FILE_NAME"]?>"></figure>
                                        </a>
                                    </li>
                                <?}?>
                            </ul>
                        <?}?>
                        <div class="date"><?=$arIdea["DATE_CREATE"]?></div>
                        <?if (is_array($arIdea["TAGS"])) {?>
                            <ul class="idea-clear tags-list">
                                <?foreach ($arIdea["TAGS"] as $tag) {?>
                                    <li style="background-color: #<?=$tag["COLOR"]?>"><?=$tag["NAME"]?></li>
                                <?}?>
                            </ul>
                        <?}?>
                        <a href="#" class="idea-btn idea-btn-default idea-btn-xs<?=$arIdea["FORUM_MSG"]["NEW"] ? " active" : ""?>">
                            <i class="icon icon-comment"></i> <span class="comments-count">
                                <?=$arIdea["FORUM_MSG"]["NEW"] ? "+".$arIdea["FORUM_MSG"]["NEW"] : $arIdea["FORUM_MSG"]["TOTAL"]?>
                            </span>
                        </a>
                        <div>
                            <div class="cell">
                                <div<?=($arIdea["CREATOR"]["PHOTO"]) ? ' class="user-layout"' : ""?>>
                                    <?if ($arIdea["CREATOR"]["PHOTO"]) {?>
                                        <div class="image">
                                            <a href="/company/personal/user/<?=$arIdea["CREATOR"]["ID"]?>/">
                                                <figure class="image-cover round">
                                                    <img src="<?=$arIdea["CREATOR"]["PHOTO"]?>" alt="<?=$arIdea["CREATOR"]["NAME"] . " " . $arIdea["CREATOR"]["LAST_NAME"]?>">
                                                </figure>
                                            </a>
                                        </div>
                                    <?}?>
                                    <div class="info">
                                        <a href="/company/personal/user/<?=$arIdea["CREATOR"]["ID"]?>/"><?=$arIdea["CREATOR"]["NAME"] . " " . $arIdea["CREATOR"]["LAST_NAME"]?></a>
                                        <div onclick="window.ITHIdea.followIdea({obj: this})" class="idea-follow follow<?=($arIdea["IS_FOLLOW"]) ? " unfollow" : ""?>" title="<?=($arIdea["IS_FOLLOW"]) ? Loc::Getmessage("IDEA_UNFOLLOW") : Loc::Getmessage("IDEA_FOLLOW")?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?}?>
        </div>
    </div>
<?} else {?>
    <div class="ideas-not-founded-msg-wrap">
        <?=Loc::GetMessage("NO_IDEAS", array("#IDEA_CREATE_LINK#" => $arResult["CREATE_LINK"]));?>
    </div>
<?}?>