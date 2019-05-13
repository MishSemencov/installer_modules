<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(_FILE_);
global $APPLICATION;

$content = '';

if(!empty($data['pageUrl']))
    $content .= Loc::Getmessage("FAST_OBJECT_COMPONENT_CONTENT_LINK").$data['pageUrl'].PHP_EOL;

if(!empty($data['images'])){
    $content .= PHP_EOL.GetMessage("FAST_OBJECT_COMPONENT_CONTENT_IMAGE").PHP_EOL;
    foreach($data['images'] as $image){
        $content .= '[IMG WIDTH='.$image['width'].' HEIGHT='.$image['height'].']'.$image['src'].'[/IMG] ';
    }
    $content .= PHP_EOL;
}
if(!empty($data['text']))
    $content .= $data['text'];

$content = trim($content, PHP_EOL);

$APPLICATION->IncludeComponent(
    "ithive:livefeed.post.form",
    "",
    [
        'FORM_TEMPLATE_ID' => 'fast_objects_livefeed_form',
        'DESTINATION_SHOW' => 'Y',
        'DESTINATION' => 'Y',
        "SHOW_MORE" => "Y",
        "PARSER" => [
            "Bold", "Italic", "Underline", "Strike", "ForeColor",
            "FontList", "FontSizeList", "RemoveFormat", "Quote", "Code",
            "InsertCut",
            "CreateLink",
            "Image",
            "Table",
            "Justify",
            "InsertOrderedList",
            "InsertUnorderedList",
            "SmileList",
            "Source",
            "UploadImage",
            "InputVideo",
            "MentionUser",
        ],
        'BUTTONS' => [
            'MentionUser',
            //'InputTag',
            'Quote',
            'CreateLink',
        ],
        "TAGS" => Array(
            "ID" => "TAGS",
            "NAME" => "TAGS",
            "VALUE" => '',
            "USE_SEARCH" => "Y",
            "FILTER" => "blog",
        ),
        "TEXT" => array(
            "INPUT_NAME" => "description",
            "VALUE" => $content,
        ),
        'ACTION' => $data['action'],
        'MODE' => $data['mode']
    ]
)?>