<?php
use Bitrix\Main\Localization\Loc;

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

<?php if(!empty($arResult['ITEMS'])):?>
    <div class="">
        <?php foreach ($arResult['ITEMS'] as $item):?>
            <div>
                <?php foreach ($item['PROPERTIES'] as $prop):?>
                <div>
                    <span><?= $prop['NAME'] ?>:</span> <?= $prop['VALUE'] ?>
                </div>
                <?php endforeach;?>
            </div>
        <?php endforeach;?>
    </div>
<?php endif;?>

<div class="">
    <form class="form" name="feedback_form" method="post" target="_top">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="IBLOCK_ID" value="<?=$arParams['IBLOCK_ID']?>">
        <?php foreach ($arResult['PROPERTIES'] as $prop):?>
            <div>
            <?php if($prop['USER_TYPE'] === 'HTML'):?>
                <textarea type="text" <?= $prop['IS_REQUIRED'] === 'Y' ? 'required="required"' : ''?> name="<?=$prop['CODE']?>" placeholder="<?=$prop['NAME']?>" value="<?= !empty($prop['DEFAULT_VALUE']) ?  unserialize($prop['DEFAULT_VALUE'])['TEXT'] : ''?>"></textarea>
            <?php else:?>
                <input type="text" <?= $prop['IS_REQUIRED'] === 'Y' ? 'required="required"' : ''?> name="<?=$prop['CODE']?>" placeholder="<?=$prop['NAME']?>" value="<?= !empty($prop['DEFAULT_VALUE']) ? $prop['DEFAULT_VALUE'] : ''?>">
            <?php endif?>
            </div>
        <?php endforeach;?>
       <input type="submit" value="<?=Loc::getMessage('MAIN_COMPONENT_FORM_TEMPLATE_SUBMIT')?>" name="feedback_form_go">
    </form>
</div>
