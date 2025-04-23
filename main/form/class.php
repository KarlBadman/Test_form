<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Iblock\PropertyTable;

class MainFormComponent extends \CBitrixComponent implements Controllerable
{
    /**
     * @var
     */
    protected $result;

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    /**
     * @return array[][]
     */
    public function configureActions()
    {
        return [
            'addForm' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ]
        ];
    }

    /**
     * @throws Main\LoaderException
     */
    protected function includeRequiredModules(){
        $arModules = ['iblock'];
        foreach ($arModules as $module)
        {
            Main\Loader::includeModule($module);
        }
    }

    /**
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function executeComponent(): void
    {

        $this->result = new Result();
        $this->includeRequiredModules();
        $this->getProperty((int)$this->arParams['IBLOCK_ID']);
        $this->getItems();

        if($this->result->isSuccess()){
            $this->arResult = $this->result->getData();
            $this->includeComponentTemplate();
        }else{
            $this->arResult['ERRORS'] = $this->result->getErrorCollection();
            $this->includeComponentTemplate('error');
        }

    }

    /**
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected function getItems(): void
    {

        $name = \Bitrix\Iblock\Iblock::wakeUp((int)$this->arParams['IBLOCK_ID'])->getEntityDataClass();
        $select = ['ID','IBLOCK_ID','ACTIVE'];
        $arProp = $this->result->getData()['PROPERTIES'];
        foreach ($arProp as $prop){
            $select[$prop['CODE'].'_'] = $prop['CODE'];
        }

        $elements = $name::getList([
            'select' => $select,
            'filter' => [
                'IBLOCK_ID' => (int)$this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
            ],
            'order' => ['SORT' => 'ASC'],
        ])->fetchAll();

        $arItems = [];

        if (!empty($elements)) {
            $arItems = array_map(function($element) use ($arProp) {
                $props = array_map(function($prop) use ($element) {
                    return [
                        'NAME'  => $prop['NAME'],
                        'CODE'  => $prop['CODE'],
                        'VALUE' => $prop['USER_TYPE'] === 'HTML' ? unserialize($element[$prop['CODE'].'_VALUE'])['TEXT'] : $element[$prop['CODE'].'_VALUE'],
                    ];
                }, $arProp);

                return [
                    'ID'         => $element['ID'],
                    'PROPERTIES' => $props
                ];
            }, $elements);
        }

        $this->result->setData(
            ['PROPERTIES'=>$arProp,'ITEMS'=>$arItems]
        );
    }

    /**
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected function getProperty(int $iblockId) : void
    {

        $propertyCodes = PropertyTable::getList(
            [
                'filter' => ['=IBLOCK_ID' => $iblockId, 'ACTIVE'=>'Y'],
                'select' => [
                    'ID',
                    'NAME',
                    'CODE',
                    'DEFAULT_VALUE',
                    'USER_TYPE',
                    'MULTIPLE',
                    'IS_REQUIRED',
                    ]
            ])->fetchAll();

        if(is_array($propertyCodes)){
            $this->result->setData(
                ['PROPERTIES'=>$propertyCodes]
            );
        }else{
            $this->result->addError(
                new Error(Loc::getMessage('MAIN_COMPONENT_FORM_CLASS_ERROR'))
            );
        }

    }

    /**
     * @return AjaxJson
     */
    public function addFormAction($form): AjaxJson
    {
        global $USER;
        $this->result = new Result();
        if(bitrix_sessid() !== $form['sessid']){
            $this->result->addError(
               'SESSION_ERROR'
            );
        }else {
            $this->includeRequiredModules();
            $this->getProperty((int)$form['IBLOCK_ID']);
            $arProp = $this->result->getData()['PROPERTIES'];
            $arNewProp = [];
            foreach ($arProp as $prop){
                $arNewProp[$prop['CODE']] = strip_tags($form[$prop['CODE']]);
            }

            $el = new \CIBlockElement;
            $arLoadProductArray = [
                "MODIFIED_BY"    => $USER->GetID(),
                "IBLOCK_ID"      => (int)$form['IBLOCK_ID'],
                "PROPERTY_VALUES"=> $arNewProp,
                "NAME"           => time(),
                "ACTIVE"         => "N",
            ];

            if($el->Add($arLoadProductArray)) {
                $this->result->setData(
                    ['msg'=>Loc::getMessage('MAIN_COMPONENT_FORM_CLASS_OK')]
                );
            }else {
                $this->result->addError(
                    new Error($el->LAST_ERROR)
                );
            }

        }

        return $this->result->isSuccess()
            ? AjaxJson::createSuccess($this->result->getData())
            : AjaxJson::createError($this->result->getErrorCollection());
    }

}