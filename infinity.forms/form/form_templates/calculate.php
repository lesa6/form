 <div class="form-large form-large--pink">
     <div class="form-background-image" style="background-image: url('/local/components/infinity.form/form/templates/.default/images/cube-back.png')"></div>
     <div class="form-large__wrapper">
         <div class="form-large__content">
             <div class="form-large__leftside">
                 <h4 class="white"><?= $arResult["FORM"]['NAME'] ?></h4>
                 <div class="example-card title-sm blur-lg">
                     <div class="card-title white text-xl">
                         Итого:
                     </div>
                     <h4 class="card-price white">
                         <span>650 560 ₽/мес.</span>
                         <div class="button-trigger" aria-describedby="tooltip">
                             <svg class="secondary" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M8.16797 4.83366H9.83464V6.50033H8.16797V4.83366ZM8.16797 8.16699H9.83464V13.167H8.16797V8.16699ZM9.0013 
                                        0.666992C4.4013 0.666992 0.667969 4.40033 0.667969 9.00033C0.667969 13.6003 4.4013 17.3337 9.0013 17.3337C13.6013 
                                        17.3337 17.3346 13.6003 17.3346 9.00033C17.3346 4.40033 13.6013 0.666992 9.0013 0.666992ZM9.0013 15.667C5.3263 
                                        15.667 2.33464 12.6753 2.33464 9.00033C2.33464 5.32533 5.3263 2.33366 9.0013 2.33366C12.6763 2.33366 15.668 5.32533 
                                        15.668 9.00033C15.668 12.6753 12.6763 15.667 9.0013 15.667Z" fill="white"></path>
                             </svg>
                             <div class="tooltip tooltip--center-bottom black" role="tooltip">
                                 <div class="text-sm bold">This is a tooltip</div>
                                 <p class="text-sm">Tooltips are used to describe or identify an element. In most scenarios, tooltips help the user understand meaning, function or alt-text.</p>
                             </div>
                         </div>
                     </h4>
                     <div class="card-description white text-sm bold">
                         Заполните форму заявки и получите спецпредложение
                     </div>
                 </div>
             </div>
             <div class="form-large__rightside">
                 <form id="fotm_<?= $arResult["FORM"]['ID'] ?>" action="<?= $APPLICATION->GetCurPage() ?>" method="POST" <?php if ($arParams["AJAX_MODE"] == "Y"): ?>data-ajax="true" <?php endif; ?> action="" method="POST" enctype="multipart/form-data" class="form-main">
                     <?= bitrix_sessid_post() ?>
                     <input type="hidden" name="submit" value="Y">
                     <input type="hidden" name="AJAX_CALL" value="<?php if ($arParams["AJAX_MODE"] == "Y"): ?>Y<?php endif; ?>">
                     <input type="hidden" name="subname" value="<?= $arResult["FORM"]['PROPERTY_SUB_NAME_VALUE'] ?>">
                     <input type="hidden" name="id" value="<?= $arResult["FORM"]['ID'] ?>">
                     <div class="form-wrapper">
                         <div class="form-inputs">
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
                         </div>
                         <div id="captcha-container_<?= $arResult["FORM"]['ID'] ?>"></div>
                         <?php $APPLICATION->IncludeComponent(
                                "infinity.ui:button",
                                "",
                                array(
                                    "STYLE_ID" => $arResult["FORM"]['PROPERTY_BUTTON_VALUE'],
                                    "TEXT" => $arResult["FORM"]['PROPERTY_TEXT_BUTTON_VALUE'],
                                    "LINK" => '#',
                                    "CLASS" => 'fotm-button',
                                ),
                                false
                            ); ?>
                         <div class="form-text white">
                             Нажимая кнопку «Оставить заявку», вы соглашаетесь с <a href="#" target="_blank">Политикой обработки персональных данных ООО «Связь ВСД»</a>
                         </div>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>