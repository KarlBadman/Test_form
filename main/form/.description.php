<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('MAIN_COMPONENT_FORM_NAME'),
	'DESCRIPTION' => Loc::getMessage('MAIN_COMPONENT_FORM_DESCRIPTION'),
	'ICON' => 'images/icon.gif',
	'CACHE_PATH' => 'Y',
	'SORT' => 10,
	'PATH' => [
		'ID' => 'main',
		'NAME' => Loc::getMessage('MAIN_COMPONENT_FORM_GROUP_NAME'),
	],
];