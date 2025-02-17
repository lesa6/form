<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

try {

    if (!Loader::includeModule("iblock")) {
        throw new Exception(Loc::getMessage("MY_FORM_CONSTRUCTOR_IBLOCK_MODULE_NOT_INSTALLED"));
    }

    $request = Application::getInstance()->getContext()->getRequest();

    $arParams["FORM_ID"] = trim($arParams["FORM_ID"]);
    if (strlen($arParams["FORM_ID"]) <= 0) {
        throw new Exception(Loc::getMessage("MY_FORM_CONSTRUCTOR_FORM_ID_EMPTY"));
    }

    if ($request->isPost() && $request["submit"] == "Y") {
        $arParams["FORM_ID"] = trim($request['id']);
        if (strlen($arParams["FORM_ID"]) <= 0) {
            throw new Exception(Loc::getMessage("MY_FORM_CONSTRUCTOR_FORM_ID_EMPTY"));
        }
    } else {
        $arParams["FORM_ID"] = trim($arParams["FORM_ID"]);
        if (strlen($arParams["FORM_ID"]) <= 0) {
            throw new Exception(Loc::getMessage("MY_FORM_CONSTRUCTOR_FORM_ID_EMPTY"));
        }
    }

    $arParams["SUCCESS_MESSAGE"] = trim($arParams["SUCCESS_MESSAGE"]);
    if (strlen($arParams["SUCCESS_MESSAGE"]) <= 0) {
        $arParams["SUCCESS_MESSAGE"] = Loc::getMessage("MY_FORM_CONSTRUCTOR_DEFAULT_SUCCESS_MESSAGE");
    }

    // $cacheKey = 'form_' . $arParams["FORM_ID"] . '_' . md5(serialize($request->toArray()));
    // if ($this->startResultCache($arParams["CACHE_TIME"], $cacheKey)) {

        // Получаем информацию о форме
        $arFilter = array(
            "IBLOCK_ID" => General\Iblock::BLOC_FORM,
            "ID" => $arParams["FORM_ID"],
            "ACTIVE" => "Y",
        );
        $rsForm = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "NAME", "PREVIEW_TEXT", "PROPERTY_FORM_TEMPLATE_SEND", "PROPERTY_FORM_TEMPLATE", "PROPERTY_SUB_NAME", "PROPERTY_ADDRESS_SHOW", "PROPERTY_EMAIL_SHOW", "PROPERTY_TEXT_BUTTON", "PROPERTY_BUTTON"));
        if ($arForm = $rsForm->GetNext()) {

            if ($arForm["PROPERTY_FORM_TEMPLATE_VALUE"]) {
                $property_enums = CIBlockPropertyEnum::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => General\Iblock::BLOC_FORM, "ID" => $arForm["PROPERTY_FORM_TEMPLATE_ENUM_ID"])); //Указываем CODE свойства
                while ($enum_fields = $property_enums->GetNext()) {
                    $arForm["PROPERTY_FORM_TEMPLATE_ENUM_XML_ID"] = $enum_fields["XML_ID"];
                }
            }

            if (empty($arForm["PROPERTY_TEXT_BUTTON_VALUE"])) {
                $arForm["PROPERTY_TEXT_BUTTON_VALUE"] = 'Оставить заявку';
            }

            if ($arForm["PROPERTY_EMAIL_SHOW_VALUE"] == 'Y' || $arForm["PROPERTY_ADDRESS_SHOW_VALUE"] == 'Y') {
                $arFilterInfo = array(
                    "IBLOCK_ID" => 1,
                    "ACTIVE" => "Y",
                );
                $rsFieldsInfo = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilterInfo, false, false, array("ID", "NAME", "PROPERTY_ADDRESS", "PROPERTY_EMAIL"));
                while ($arFieldInfo = $rsFieldsInfo->GetNext()) {

                    $arForm["EMAIL"] = $arFieldInfo['PROPERTY_EMAIL_VALUE'];
                    $arForm["ADDRESS"] = $arFieldInfo['PROPERTY_ADDRESS_VALUE'];
                }
            }

            $arResult["FORM"] = $arForm;

            // Получаем поля формы
            $arFields = array();
            $arFilter = array(
                "IBLOCK_ID" => 11,
                "PROPERTY_FORM" => $arForm["ID"],
                "ACTIVE" => "Y",
            );
            $rsFields = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), $arFilter, false, false, array("ID", "NAME", "PROPERTY_TYPE_INPUT", "PROPERTY_VALIDATOR", "PROPERTY_REQUIRED", "CODE"));
            while ($arField = $rsFields->GetNext()) {

                if ($arField["PROPERTY_TYPE_INPUT_VALUE"]) {
                    $property_enums = CIBlockPropertyEnum::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => 11, "ID" => $arField["PROPERTY_TYPE_INPUT_ENUM_ID"])); //Указываем CODE свойства
                    while ($enum_fields = $property_enums->GetNext()) {
                        $arField["PROPERTY_TYPE_INPUT_ENUM_XML_ID"] = $enum_fields["XML_ID"];
                    }
                }

                if ($arField["PROPERTY_VALIDATOR_VALUE"]) {
                    $property_enums = CIBlockPropertyEnum::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => 11, "ID" => $arField["PROPERTY_VALIDATOR_ENUM_ID"])); //Указываем CODE свойства
                    while ($enum_fields = $property_enums->GetNext()) {
                        $arField["PROPERTY_VALIDATOR_ENUM_XML_ID"] = $enum_fields["XML_ID"];
                    }
                }

                $arFields[] = $arField;
            }

            $arResult["FIELDS"] = $arFields;
        } else {
            $this->abortResultCache();
            ShowError(Loc::getMessage("MY_FORM_CONSTRUCTOR_FORM_NOT_FOUND"));
        }
    // }

    // Обработка отправки формы
    if ($request->isPost() && $request["submit"] == "Y") {

        $arResult["ERRORS"] = array();

        // Получаем префикс для названий полей
        $formPrefix = "form_" . $arParams["FORM_ID"] . "_";

        // Валидация полей
        foreach ($arResult["FIELDS"] as $arField) {
            $fieldCode = $arField["CODE"];
            $prefixedFieldCode = $formPrefix . $fieldCode; // Создаем имя поля с префиксом
            $fieldValue = trim($request[$prefixedFieldCode]); // Получаем значение из POST


            if ($arField["PROPERTY_REQUIRED_VALUE"] == "Y" && strlen($fieldValue) <= 0) {
                $arResult["ERRORS"][] = array('name' => $arField["CODE"], 'error' => Loc::getMessage("MY_FORM_CONSTRUCTOR_FIELD_REQUIRED", array("#NAME#" => $arField["NAME"])));
            }

            // Дополнительная валидация (email)
            if ($arField["PROPERTY_TYPE_INPUT_ENUM_XML_ID"] == "text" && strpos($arField["CODE"], "email") !== false && !check_email($fieldValue)) { // Проверка email
                $arResult["ERRORS"][] = array('name' => $arField["CODE"], 'error' => Loc::getMessage("MY_FORM_CONSTRUCTOR_INVALID_EMAIL", array("#NAME#" => $arField["NAME"])));
            }

            // Дополнительная валидация (на латиницу)
            if ($arField["PROPERTY_TYPE_INPUT_ENUM_XML_ID"] == "text" && $arField["PROPERTY_VALIDATOR_ENUM_XML_ID"] == "lat") { // Проверка латиницу
                $value = trim($fieldValue); // Remove leading/trailing whitespace
                if (strlen($value) > 0) {
                    if (!preg_match('/^[\p{Cyrillic}\s\-]+$/u', $value)) {
                        $arResult["ERRORS"][] = array('name' => $arField["CODE"], 'error' => Loc::getMessage("MY_FORM_CONSTRUCTOR_INVALID_CYRILLIC", array("#NAME#" => $arField["NAME"])));
                    }
                }
            }

            //Сохраняем значения полей
            $arResult["VALUES"][$prefixedFieldCode] = $fieldValue;
        }

        if (empty($arResult["ERRORS"])) {
            // Сохранение результата в инфоблок
            $el = new CIBlockElement;

            //Сохраняем значения полей как свойства элемента.
            foreach ($arResult["VALUES"] as $key => $value) {
                $propsElement .= $key . ": " . $value . ", ";
            }

            $props = array(
                "FORM" => $arParams["FORM_ID"],
                "RESULT" => $propsElement,
            );

            $arLoadProductArray = array(
                "IBLOCK_ID"       => 10, // Замените на ID вашего инфоблока "Результаты формы"
                "PROPERTY_VALUES" => $props,
                "NAME"           => $arResult["FORM"]['NAME'] . ' - ' . date("d.m.Y H:i:s"),
                "ACTIVE"         => "Y",
            );

            if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                $arResult["SUCCESS"] = true;

                if ($arForm["FORM_TEMPLATE_SEND"] > 0) {
                    foreach ($arResult["FIELDS"] as $arField) {
                        $arEventFields[$arField["CODE"]] = $arResult["VALUES"][$arField["CODE"]];
                    }

                    $event = new Event(array(
                        "EVENT_NAME" => "FEEDBACK_FORM",
                        "LID" => SITE_ID,
                        "C_FIELDS" => $arEventFields,
                        "MESSAGE_ID" => $arForm["FORM_TEMPLATE_SEND"],
                    ));

                    if (!$event->send()) {
                        $arResult["ERRORS"][] = Loc::getMessage("MY_FORM_CONSTRUCTOR_EMAIL_SEND_ERROR");
                    }
                }
            } else {
                $arResult["ERRORS"][] = Loc::getMessage("MY_FORM_CONSTRUCTOR_SAVE_ERROR") . $el->LAST_ERROR;
            }

            if ($arResult["SUCCESS"]) {
                //Очистка полей
                foreach ($arResult["FIELDS"] as $arField) {
                    $arResult["VALUES"][$arField["CODE"]] = "";
                }
            }
        }

        //Отправка AJAX ответа
        if ($request["AJAX_CALL"] == "Y") {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            header('Content-Type: application/json');
            echo json_encode([
                "SUCCESS" => $arResult["SUCCESS"] ?? false,
                "ERRORS" => $arResult["ERRORS"] ?? [],
                "VALUES" => $arResult["VALUES"] ?? [],
            ]);
            CMain::FinalActions();
            die();
        }
    }
} catch (Exception $e) {
    if ($request["AJAX_CALL"] == "Y") {
        header('Content-Type: application/json');
        echo json_encode(["ERRORS" => [$e->getMessage()]]);
        die();
    } else {
        ShowError($e->getMessage());
    }
}

$this->arResult = $arResult;
$this->includeComponentTemplate();
