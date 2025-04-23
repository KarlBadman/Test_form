<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @var $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var string $component
 * @var string $templateFolder
 */

?>
 <div>
     <?php foreach ($arResult['ERROR'] as $error):?>
        <div><?=$error?></div>
     <?php endforeach;?>
 </div>
