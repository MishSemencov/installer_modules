<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__."/functions.php";
global $USER;
CModule::IncludeModule("tasks");
$tasks = getTasks();
if($_GET['action'] == "defer")
{
    $testTask = new CTaskItem($_GET['taskID'],$USER -> getId());
    $taskData = $testTask -> getData();
    $testTask -> defer();
    if($taskData['DEADLINE'] != null) {
        $date = date("d.m.Y H:i:s", (strtotime($taskData['DEADLINE']) + $_GET['time']));
        $testTask->update(["DEADLINE" => $date]);
    }
    $tasks[] = [
        "taskID" => $_GET['taskID'],
        "userID" => $USER -> getId(),
        "expire" => time() + $_GET['time']
    ];
    save($tasks);
}

if($_GET['action'] == "returnInWork")
{
    returnInWork();
}
