<form id="fotm_<?= $arResult["FORM"]['ID'] ?>" action="<?= $APPLICATION->GetCurPage() ?>" method="POST" <?php if ($arParams["AJAX_MODE"] == "Y"): ?>data-ajax="true" <?php endif; ?> action="" method="POST" enctype="multipart/form-data" class="form form--call">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="submit" value="Y">
    <input type="hidden" name="AJAX_CALL" value="<?php if ($arParams["AJAX_MODE"] == "Y"): ?>Y<?php endif; ?>">
    <input type="hidden" name="subname" value="<?= $arResult["FORM"]['PROPERTY_SUB_NAME_VALUE'] ?>">
    <input type="hidden" name="id" value="<?= $arResult["FORM"]['ID'] ?>">
    <?php foreach ($arResult["FIELDS"] as $arField): ?>
        <?php
        $required = ($arField["PROPERTY_REQUIRED_VALUE"] == "Да") ? "required" : "";
        ?>
        <?php switch ($arField["PROPERTY_TYPE_INPUT_ENUM_XML_ID"]):
            case "textarea": ?>
                <textarea name="form_<?= $arResult["FORM"]['ID'] ?>_<?= htmlspecialcharsbx($arField["CODE"]) ?>" class="form-control" id="<?= htmlspecialcharsbx($arField["CODE"]) ?>" <?= $required ?>><?= $arResult["VALUES"][$arField["CODE"]] ?></textarea>
            <?php break;
            case "select": ?>
                <select name="form_<?= $arResult["FORM"]['ID'] ?>_<?= htmlspecialcharsbx($arField["CODE"]) ?>" class="form-control" id="<?= htmlspecialcharsbx($arField["CODE"]) ?>" <?= $required ?> <?= $hint ?>>
                    <option value=""><?= Loc::getMessage("MY_FORM_CONSTRUCTOR_SELECT_OPTION") ?></option>
                    <?php
                    $values = explode(";", $arField["PROPERTY_VALUES_VALUE"]);
                    foreach ($values as $value): ?>
                        <option value="<?= htmlspecialcharsbx(trim($value)) ?>" <?php if ($arResult["VALUES"][$arField["CODE"]] == trim($value)): ?>selected<?php endif; ?>><?= htmlspecialcharsbx(trim($value)) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php break;
            case "checkbox": ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="form_<?= $arResult["FORM"]['ID'] ?>_<?= htmlspecialcharsbx($arField["CODE"]) ?>" value="Y" <?php if ($arResult["VALUES"][$arField["CODE"]] == "Y"): ?>checked<?php endif; ?> <?= $hint ?>>
                        <?= htmlspecialcharsbx($arField["NAME"]) ?>
                    </label>
                </div>
            <?php break;
            case "radio": ?>
                <?php
                $values = explode(";", $arField["PROPERTY_VALUES_VALUE"]);
                foreach ($values as $value): ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="form_<?= $arResult["FORM"]['ID'] ?>_<?= htmlspecialcharsbx($arField["CODE"]) ?>" value="<?= htmlspecialcharsbx(trim($value)) ?>" <?php if ($arResult["VALUES"][$arField["CODE"]] == trim($value)): ?>checked<?php endif; ?> <?= $hint ?>>
                            <?= htmlspecialcharsbx(trim($value)) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php break;
            default: ?>
                <div class="input">
                    <input <?php if (!empty($arField["PROPERTY_VALIDATOR_ENUM_XML_ID"])): ?>data-valid='<?= $arField["PROPERTY_VALIDATOR_ENUM_XML_ID"] ?>' <?php endif; ?> class="input-item input__field" <?= $required ?> type="<?= $arField["PROPERTY_TYPE_INPUT_ENUM_XML_ID"] ?>" name="form_<?= $arResult["FORM"]['ID'] ?>_<?= htmlspecialcharsbx($arField["CODE"]) ?>" value="<?= $arResult["VALUES"][$arField["CODE"]] ?>" placeholder="<?= htmlspecialcharsbx($arField["NAME"]) ?><?php if ($required): ?>*<?php endif; ?>">
                </div>
        <?php break;
        endswitch; ?>
    <?php endforeach; ?>
    <div id="captcha-container_<?= $arResult["FORM"]['ID'] ?>"></div>
    <?php $APPLICATION->IncludeComponent(
        "infinity.ui:button",
        "",
        array(
            "STYLE_ID" => $arResult["FORM"]['PROPERTY_BUTTON_VALUE'],
            "TEXT" => $arResult["FORM"]['PROPERTY_TEXT_BUTTON_VALUE'],
            "LINK" => '#',
        ),
        false
    ); ?>
</form>