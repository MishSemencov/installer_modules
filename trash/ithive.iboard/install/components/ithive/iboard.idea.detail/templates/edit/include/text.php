<?if ($isCreator) {?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.post.form",
        "",
        Array(
            "FORM_ID" => "idea_form_". $arParams["FORM_ID"],
            "SHOW_MORE" => "N",
            "PARSER" => array(
                "Bold",
                "Italic",
                "Underline",
                "Strike",
                "ForeColor",
                "FontList",
                "FontSizeList",
                "RemoveFormat",
                "Quote",
                "Code",
                "CreateLink",
                "Image",
                "Table",
                "Justify",
                "InsertOrderedList",
                "InsertUnorderedList",
                "SmileList",
                "Source",
                "InsertVideo",
                "More"
            ),
            "BUTTONS" => array(
                "UploadImage",
                "UploadFile",
                "InputVideo",
            ),
            "LHE" => array(
                'id' => $arParams["FORM_ID"],
                "LHEJsObjName" => "JS_IDEA_TEXT_".$arParams["FORM_ID"],
                'bResizable' => true,
                'bAutoResize' => true,
                "height" => 500,
                "bbCode" => 1
            ),
            "NAME_TEMPLATE" => "#NAME# #LAST_NAME#",
            "TEXT" => Array(
                "ID" => "IDEA_TEXT",
                "NAME" => "IDEA_TEXT",
                "VALUE" => $arResult["IDEA"]["~TEXT"],
                "SHOW" => "Y",
                "HEIGHT" => "300px"
            ),
            "ADDITIONAL" => $arResult["ADDITIONAL_BTN_HTML"],
            "UPLOAD_FILE" => array(
                "INPUT_NAME" => 'IDEA_FILES',
                "INPUT_VALUE" => $arResult["IDEA"]["FILES"]["ID"],
                //"MAX_FILE_SIZE" => 5000000,
                "MULTIPLE" => "Y",
                "MODULE_ID" => "ithive.iboard",
                "ALLOW_UPLOAD" => "A",
                "ALLOW_UPLOAD_EXT" => "Y"
            ),
            "PIN_EDITOR_PANEL" => "Y"
        )
    );
    ?>
<?} else {?>
    <div class="idea-text-wrap">
        <?=$arResult["IDEA"]["TEXT"]?>
    </div>
<?}?>
<hr>