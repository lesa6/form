<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (empty($arResult["FORM"]['ID'])) {
    die("Ошибка: ID формы не определен.");
}
if (empty($arResult["FORM"]["PROPERTY_FORM_TEMPLATE_ENUM_XML_ID"])) {
    die("Ошибка: Шаблон формы не определен.");
}
?>
<? switch ($arResult["FORM"]["PROPERTY_FORM_TEMPLATE_ENUM_XML_ID"]) {
    case "calculate":
        include(__DIR__ . "/form_templates/calculate.php");
        break;
    case "banner":
        include(__DIR__ . "/form_templates/banner.php");
        break;
    case "standart":
        include(__DIR__ . "/form_templates/standart.php");
        break;
    case "modal":
        include(__DIR__ . "/form_templates/modal.php");
        break;
    default:
        echo "Неизвестный шаблон формы";
} ?>
<div id="succes-content" style="display:none;max-width:600px;">
    <h3><?= $arParams["SUCCESS_MESSAGE_TITLE"] ?></h3>
    <p>
        <?= $arParams["SUCCESS_MESSAGE_TEXT"] ?>
    </p>
</div>

<!--Отправка формы-->
<?php if ($arParams["AJAX_MODE"] == "Y"): ?>
    <script>
        (function() {
            let widgetId; // Объявляем widgetId внутри замыкания

            function onloadFunction() {
                if (!window.smartCaptcha) {
                    return;
                }

                widgetId = window.smartCaptcha.render('captcha-container_<?= $arResult["FORM"]['ID'] ?>', { // Добавляем уникальный ID для контейнера
                    sitekey: 'ysc1_bobA4ThlfyVb3dEteHdrT5cAvB3lkxh5chMSjuP60846cf7f',
                    invisible: true, // Сделать капчу невидимой
                    callback: callback,
                });
            }

            function handleSubmit(event) {
                event.preventDefault();
                if (!window.smartCaptcha) {
                    return;
                }

                window.smartCaptcha.execute(widgetId);
            }

            function callback(token) {
                if (typeof token === "string" && token.length > 0) {

                    console.log(token);
                    const form = document.getElementById('fotm_<?= $arResult["FORM"]['ID'] ?>');
                    console.log(form);

                    // Функция для удаления span с ошибками и классов input--error
                    function clearErrors() {
                        const errorSpans = form.querySelectorAll('.input__hint');
                        if(errorSpans){
                            errorSpans.forEach(span => span.remove());
                        }

                        const errorInputs = form.querySelectorAll('.input--error');
                        if(errorSerrorInputspans){
                            errorSpans.forEach(span => span.remove());
                        }
                        errorInputs.forEach(inputWrapper => inputWrapper.classList.remove('input--error'));
                    }
                    // Очищаем ошибки перед отправкой запроса
                    clearErrors();

                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function() {
                        BX.closeWait(); // Скрываем индикатор загрузки после получения ответа
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                try {
                                    const data = JSON.parse(xhr.responseText);
                                    console.log(data)
                                    if (data.SUCCESS) {
                                        Fancybox.show([{
                                            src: "#succes-content",
                                            type: "inline"
                                        }]);
                                        form.reset();
                                    } else if (data.ERRORS && data.ERRORS.length > 0) {
                                        data.ERRORS.forEach(function(error) {
                                            const inputElement = document.querySelector(`#fotm_<?= $arResult["FORM"]['ID'] ?> input[name="${error.name}"]`);
                                            if (inputElement) {
                                                const inputWrapper = inputElement.closest('.input');
                                                if (inputWrapper) {
                                                    inputWrapper.classList.add('input--error');
                                                    // Создаем элемент span для текста ошибки
                                                    const errorSpan = document.createElement('span');
                                                    errorSpan.classList.add('input__hint');
                                                    errorSpan.textContent = error.error;
                                                    inputElement.insertAdjacentElement('afterend', errorSpan);
                                                } else {
                                                    console.warn(`Не найден родительский элемент с классом .input для поля ${error.name}`);
                                                }
                                            } else {
                                                console.warn(`Не найден input с именем ${error.name} и id fotm_<?= $arResult["FORM"]['ID'] ?>`);
                                            }
                                        });
                                    } else {
                                        console.error('Неизвестная ошибка');
                                    }
                                } catch (e) {
                                    console.error("Ошибка при парсинге JSON:", e);
                                    return;
                                }
                            } catch (e) {
                                console.error('Ошибка при парсинге JSON: ', e);
                            }
                        } else {
                            console.error('Ошибка: ' + xhr.status);
                        }
                    };
                    xhr.onerror = function() {
                        console.error('Ошибка соединения');
                    };
                    xhr.send(formData);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('fotm_<?= $arResult["FORM"]['ID'] ?>');
                if (!form) {
                    console.error("Форма с ID 'fotm_<?= $arResult["FORM"]['ID'] ?>' не найдена.");
                    return;
                }

                if (form) {
                    form.addEventListener('submit', handleSubmit); // Привязываем handleSubmit к кнопке
                } else {
                    console.warn('Кнопка отправки формы не найдена.');
                }

                if (!window.smartCaptcha) {
                    console.error("Скрипт SmartCaptcha не загружен.");
                    return;
                }

                onloadFunction(); // Вызываем onloadFunction внутри замыкания
            });

        })();
    </script>
<?php endif; ?>