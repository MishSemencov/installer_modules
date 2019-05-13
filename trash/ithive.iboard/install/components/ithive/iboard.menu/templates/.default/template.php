<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="iboard-menu-wrap">
    <?foreach($arResult['ITEMS'] as $item){?>
        <a href="<?=$item['URL']?>" class="idea-btn<?=$item['ADD_CLASS']?>"><?=$item['TITLE']?></a>
    <?}?>
</div>
