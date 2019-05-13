# README #

### How do I get set up? ###

* Clone repository
* Copy it to /local/modules/
* Get composer install
* Add routing


### Basic routing (1C-Bitrix) ###

Add to urlrewrite.php at project root next:
		
```
#!php

$arNewUrlRewrite[] = array(
	"CONDITION" => "#^/api/([\\w_-]+)/([\\w_-]+)/([\\w_-]+)/(.*)#",
	"RULE" => "app=\$1&version=\$2&entity=\$3&action=\$4",
	"ID" => "",
	"PATH" => "/api/index.php",
);
```