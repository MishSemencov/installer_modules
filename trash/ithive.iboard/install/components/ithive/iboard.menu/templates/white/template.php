<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<nav>
    <?foreach($arResult['ITEMS'] as $item){?>
        <a href="<?=$item['URL']?>" class="idea-btn"><?=$item['TITLE']?></a>
    <?}?>
</nav>

