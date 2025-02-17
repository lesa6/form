<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "FORM_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("MY_FORM_CONSTRUCTOR_FORM_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "SUCCESS_MESSAGE_TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("MY_FORM_CONSTRUCTOR_SUCCESS_MESSAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("MY_FORM_CONSTRUCTOR_DEFAULT_SUCCESS_MESSAGE"),
        ),
        "SUCCESS_MESSAGE_TEXT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("MY_FORM_CONSTRUCTOR_SUCCESS_MESSAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("MY_FORM_CONSTRUCTOR_DEFAULT_SUCCESS_MESSAGE"),
        ),
        "CACHE_TIME" => array("DEFAULT" => 3600),
    ),
);

$arComponentParameters["PARAMETERS"]["AJAX_MODE"] = array();
