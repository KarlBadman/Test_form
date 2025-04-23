<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);


$arComponentParameters = [
	'PARAMETERS' => [
		'IBLOCK_ID' => [
			'PARENT' => 'BASE',
            "NAME" => Loc::getMessage('MAIN_COMPONENT_FORM_IBLOCK_ID'),
			'TYPE' => 'INT',
		],
        "CACHE_TIME" =>[
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('MAIN_COMPONENT_FORM_CACHE_TIME'),
            "TYPE" => "NUMBER",
            "DEFAULT"=>"0"
        ],
	]
];