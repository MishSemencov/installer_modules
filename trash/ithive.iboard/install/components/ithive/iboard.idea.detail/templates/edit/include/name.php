<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<?if ($isCreator) {?>
    <input class="idea-name-edit" type="text" name="IDEA[NAME]" value="<?=$arResult["IDEA"]["NAME"]?>" placeholder="<?=Loc::Getmessage("IDEA_NAME")?>">
<?} else {?>
    <b> <?=$arResult["IDEA"]["NAME"]?></b>
<?}?>