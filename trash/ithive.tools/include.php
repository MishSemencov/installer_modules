<?
function agentUserImport() {
	require_once($_SERVER["DOCUMENT_ROOT"]."/test/smbclass.php");
	$_POST['login'] = 'Portal.Exch';
	$_POST['pass'] = 'Secret!Q2w3e4r';
	$arError = false;
	$username = $_POST["login"];
	$password = $_POST["pass"];
	$smb = new smbclient('\\\172.27.0.202\\ex', $username, $password);
	$file = $smb->dir("\\users");
	$file = $smb->get("\\users\\".date("Ymd")."_FULL.xml", $_SERVER["DOCUMENT_ROOT"]."/upload/zup_exchange/employees.xml");
	
	if ($file) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
		$file = $_SERVER['DOCUMENT_ROOT'] . '/upload/zup_exchange/employees.xml';
		$arUsers = array();
		if (file_exists($file)) {
			$xml = new CDataXML($file);
			$xml->Load($file);
			if ($node = $xml->SelectNodes('/Выгрузка_ДО_Для_Портала/ТаблицаСотрудников')) {
			   $nodes = $node->children;
			   foreach ($nodes as $k => $v) {
				foreach ($v->children as $props) {
					$arUsers[$k][$props->name] = $props->content;
				}
			   }
			}
		} else {
			$arError[] = "Файла не существует";
		}
		if (is_array($arUsers)) {
			$usersInFile = array();
			$usersUpd = array();
			foreach ($arUsers as $user) {
				$usersInFile[] = $user["ТабельныйНомер"];
				$arFields = array(
					"NAME" => $user["Имя"],
					"LAST_NAME" => $user["Фамилия"],
					"SECOND_NAME" => $user["Отчество"],
					"LOGIN" => $user["ЛогинАД"],
					"WORK_PHONE" => $user["ТелефонРабочий"],
					"EMAIL" => $user["ЭлектроннаяПочта"],
					"WORK_DEPARTMENT" => $user["Подразделение"],
					"WORK_POSITION" => $user["Должность"],
					"WORK_COMPANY" => $user["МестоРаботы"],
					"PERSONAL_GENDER" => $user["Пол"] == 'Мужской'?'M':'F',
					"PERSONAL_BIRTHDAY" => $user["ДатаРождения"],
					"EMAIL" => $user["ЭлектроннаяПочта"],
					"UF_TABLE_NUM" => $user["ТабельныйНомер"], 
					"UF_PHONE_INNER" => $user["ДобавочныйНомер"], 
					"UF_ENG_NAME" => $user["ИмяАнгл"], 
					"UF_ENG_LAST_NAME" => $user["ФамилияАнгл"], 
					"UF_MOBILE_KOD" => $user["КодМобильногоПриложения"], 
					"UF_SIGN" => $user["Подпись"], 
					"WORK_STREET" => $user["АдресРаботы"]
				);
				if (strlen($user["ДатаУвольнения"]) > 0) {
					$arFields["ACTIVE"] = 'N';
					
				}
				if (mb_substr($user["ТабельныйНомер"], 0, 1) == 'D') { //Гости, для них не нужен импорт
					$arFields["UF_TABLE_NUM"] = $user["ТабельныйНомер"];
				}; 
				if (!in_array(mb_substr($user["ТабельныйНомер"], 0, 1), array('D', 'S', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'V', 'Z'))) {
					$wrongUser[] = $user["ТабельныйНомер"];
					CEventLog::Add(array(
				            "SEVERITY" => "ERROR",
				            "AUDIT_TYPE_ID" => "USERERROR",
				            "MODULE_ID" => "ithive.tools",
				            "ITEM_ID" => 1,
				            "DESCRIPTION" => "Wrong table number: ".$user["ТабельныйНомер"],
				        ));	
					continue;
				}; 
			 	$rsUsers = \Bitrix\Main\UserTable::getList(
					array('filter' => array("UF_TABLE_NUM" => $user["ТабельныйНомер"])),
					array('select' => array("ID"))
				);
				if (intval($rsUsers->getSelectedRowsCount()) == 0) :
				 	$rsUsers = \Bitrix\Main\UserTable::getList(
						array('filter' => array("LOGIN" => $user["ЛогинАД"])),
						array('select' => array("ID"))
					);
				endif;

				$newUser = new CUser;
				if ($arUser = $rsUsers->Fetch()) {
					$usersUpd[] = $arUser["ID"];
					if ($arUser["ID"] != 1) { // Чтобы админ не обновлялся
						CEventLog::Add(array(
					            "SEVERITY" => "WARNING",
					            "AUDIT_TYPE_ID" => "USERUPDATE",
					            "MODULE_ID" => "ithive.tools",
					            "ITEM_ID" => $arUser["ID"],
					            "DESCRIPTION" => "Updated: ".$arUser["ID"],
					        ));	
					$newUser->Update($arUser["ID"], $arFields);

					}
				} else {
				/*
					$password = mb_substr(md5(uniqid(rand(), true)), 0, 10);
					$arFields["PASSWORD"] = $password;
					$arFields["CONFIRM_PASSWORD"] = $password;
					$USER_ID = $newUser->Add($arFields);
					if ($USER_ID) {
					        echo 'Пользователь добавлен '.$USER_ID.'<br>';
						echo 'Пароль'.$password.'<br>';
					} else {
					        echo $cUser->LAST_ERROR;
					}
				*/
				}
			}
		}

	 	$rsUsers = \Bitrix\Main\UserTable::getList(
			array('filter' => array("!ID" => $usersUpd)),
			array('select' => array("ID", "NAME", "LOGIN", "LAST_NAME", "SECOND_NAME"))
		);
		$message = '';
		while ($arUser = $rsUsers->Fetch()) {
			$message .= $arUser["LAST_NAME"]." ".$arUser["NAME"]." ".$arUser["SECOND_NAME"].";".$arUser["LOGIN"].";".$arUser["EMAIL"].";".($arUser["ACTIVE"]=='Y'?'Активный':'Неактивный')."<br>";
		}
		
		if (strlen($message) > 0) {
			$headers = array(
			    'MIME-Version' => '1.0',
			    'Content-type' => 'text/html; charset=UTF-8',
			    'X-Mailer' => 'PHP/' . phpversion()
			);
			
			$headers = array_reduce(array_keys($headers), function($result, $key) use ($headers) { return $key . ": " . $headers[$key] . PHP_EOL . $result; });
			
			$to = 'bubenschikov@bushe.ru';
			$subj = "Пользователи, которые есть на портале, но их нет в файле импорта";
			$res = bxmail($to, $subj, $message, $headers);
		}
	
	} else {
		$arError[] = "Нет файла в каталоге smb \\users\\".date("Ymd")."_FULL.xml";
		$arError[] = $smb->get_last_cmd_stdout();
	}

	if (!empty($arError)) :
		CEventLog::Add(array(
	            "SEVERITY" => "ERROR",
	            "AUDIT_TYPE_ID" => "USERIMPORT",
	            "MODULE_ID" => "ithive.tools",
	            "ITEM_ID" => 1,
	            "DESCRIPTION" => print_r($arError, true),
	        ));		
	endif;
   return "agentUserImport();";
}