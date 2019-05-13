<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

class AgentClass
{
    public static function Agent()
    {
        require_once $_SERVER['DOCUMENT_ROOT']."/local/deferIts/functions.php";
        returnInWork();
        return "AgentClass::Agent();";
    }
}