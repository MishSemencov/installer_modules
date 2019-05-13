<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
CJSCore::Init(array("iboard_core"));
global $arrIdeasFilter;
if (isset($arrIdeasFilter) and count($arrIdeasFilter) > 0 && count($arResult["IDEAS"]) == 0) {?>
    <div class="ideas-not-founded-msg-wrap"><?=Loc::Getmessage("IDEAS_BY_FILTER_NOT_FOUNDED")?></div>
<?} elseif (!empty($arResult["CATEGORIES"])) {?>
    <div class="ideas-scroller-container">
        <span class="ideas-list-left"></span>
        <span class="ideas-list-right"></span>
        <div class="ideas-scroller" id="ideas-scroller">
            <div class="scroller" style="width: <?=count($arResult["CATEGORIES"]) * 300 . "px"?>">
                <div class="row collapsed-row">
                    <?foreach ($arResult["CATEGORIES"] as $categoryId => $arCategory) {
                        $categoryElementsCnt = count($arCategory["ELEMENTS"]);
                        if ($categoryElementsCnt == 0 && count($arrIdeasFilter) > 0) continue;
                        $size = (strlen($arCategory["NAME"]) - 2 < 1) ? 1 : strlen($arCategory["NAME"]) - 2;?>
                        <div class="col-xs-2 idea-category-wrap">
                            <div class="idea-title" style="background-color: #<?=$arCategory["COLOR"]?>">
                                <input type="hidden" class="idea-category-id-input" name="IDEA_CATEGORY_ID" value="<?=$arCategory["ID"]?>" data-value="<?=$arCategory["ID"]?>">
                                <input type="hidden" class="idea-category-color-input" name="IDEA_CATEGORY_COLOR_<?=$arCategory["ID"]?>" value="<?=$arCategory["COLOR"]?>" data-value="<?=$arCategory["ID"]?>">
                                <input class="idea-title-input" type="text" value="<?=$arCategory["NAME"]?>" disabled="" size="<?=$size?>" onkeyup="window.ITHIdea.confirmInputEditCategory({obj: this, event: event})">
                                <span id="count<?=$arCategory["ID"]?>" class="idea-count"><?=$categoryElementsCnt?></span>
                                <span class="idea-sort-edit">
                                    <span onclick="window.ITHIdea.sortCategory({obj: this, mode: 'up'})" class="idea-sort-btn idea-sort-up align-right"><</span>
                                    <span onclick="window.ITHIdea.sortCategory({obj: this, mode: 'down'})" class="idea-sort-btn idea-sort-down align-right">></span>
                                </span>
                                <span onclick="window.ITHIdea.createColorPopup({obj: this})" class="idea-color-edit align-right"></span>
                                <span onclick="window.ITHIdea.editCategory({obj: this})" class="idea-category-edit align-right"
                                    <?=($arCategory["SYSTEM"] != 1) ? "" : "style='right: 10px'"?>></span>
                                <?if ($arCategory["SYSTEM"] != 1) {?>
                                    <span onclick="window.ITHIdea.onDeleteCategory({obj: this})" class="idea-category-delete"></span>
                                <?}?>
                                <span onclick="window.ITHIdea.confirmEditCategory({obj: this})" class="idea-category-confirm"></span>
                            </div>
                            <form action="#" class="ideas-form">
                                <div>
                                    <a href="<?=$arResult["CREATE_LINK"] . "?CID=" . $categoryId?>" type="button" class="idea-btn add-idea-btn">
                                    </a>
                                </div>
                                <div class="empty-text<?=$categoryElementsCnt > 0 ? " hidden" : ""?>"><?=Loc::Getmessage("CATEGORY_EMPTY")?></div>
                                <ul class="idea-clear ideas-list"
                                    data-onstart="startCallback"
                                    data-ondrag="dragCallback"
                                    data-ondrop="dropCallback"
                                    data-classname="primary"
                                >
                                    <?if ($categoryElementsCnt > 0) {?>
                                            <?foreach ($arCategory["ELEMENTS"] as $ideaId) {
                                                $arIdea = $arResult["IDEAS"][$ideaId];?>
                                                <li>
                                                    <div class="idea<?=($arIdea["MIN_LIST"] != 1) ? " active" : ""?>" style="border-color: #<?=$arCategory["COLOR"]?>">
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
                                                                <button type="button" class="toggler idea-set-minimized" data-type="list">
                                                                    <i class="icon icon-menu<?=($arIdea["MIN_LIST"] != 1) ? "-opened" : ""?>"></i>
                                                                    <i class="icon icon-menu<?=($arIdea["MIN_LIST"] == 1) ? "-opened" : ""?>"></i>
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
                                                        <a href="<?=$arIdea["DETAIL_PAGE_URL"]?>#idea-comments-block" class="idea-btn idea-btn-default idea-btn-xs idea-comment-btn<?=$arIdea["FORUM_MSG"]["NEW"] ? " active" : ""?>">
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
                                                </li>
                                            <?}?>
                                    <?}?>
                                </ul>
                            </form>
                        </div>
                    <?}?>
                </div>
            </div>
        </div>
    </div>
<?} else {?>
    <div class="ideas-not-founded-msg-wrap">
        <?=Loc::GetMessage("NO_IDEAS", array("#IDEA_CREATE_LINK#" => $arResult["CREATE_LINK"]));?>
    </div>
<?}?>
