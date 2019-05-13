<?php
function returnInWork()
{
    CModule::IncludeModule("tasks");
    $tasks = getTasks();
    foreach($tasks as $id => $task)
    {
        if($task['expire'] < time()){
            unset($tasks[$id]);
            $testTask = new CTaskItem($task['taskID'],$task['userID']);
            $testTask -> startExecution();
        }
    }
    save($tasks);
    return "returnInWork();";
}

function save($taskAr)
{
    return file_put_contents(__DIR__."/tasks.json",json_encode($taskAr));
}

function getTasks()
{
    return json_decode(file_get_contents(__DIR__."/tasks.json"),true);
}