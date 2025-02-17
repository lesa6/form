document.addEventListener("DOMContentLoaded", function (e) {
    document.querySelectorAll('.button-trigger').forEach(item => {

        const tooltip = item.querySelector('.tooltip');
        let popperInstance; // Экземпляр Popper

        item.addEventListener('mouseenter', () => {
            tooltip.classList.add('show');
            const arrow = document.querySelector('#arrow');
            popperInstance = Popper.createPopper(item, tooltip, {
                placement: 'top', // Расположение тултипа относительно кнопки
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, 10],
                        },
                    },
                    {
                        name: 'arrow',
                        options: {
                            element: '.tooltip-arrow',
                        }
                    },
                ],
            });
        });

        item.addEventListener('mouseleave', () => {
            tooltip.classList.remove('show');

            if (popperInstance) {
                popperInstance.destroy();
                popperInstance = null;
            }
        });
    });

    function validator() {
        const valid = document.querySelectorAll("[data-valid]");

        valid.forEach(element => {
            const maskType = element.dataset.valid;

            switch (maskType) {
                case 'tel':
                    const telMask = new Inputmask("+7 (999) 999-99-99", { clearIncomplete: true });
                    telMask.mask(element);
                    break;

                case 'INN':
                    const innMask = new Inputmask({
                        mask: [
                            "9{10}",
                            "9{12}"
                        ],
                        greedy: false,
                        rightAlign: false,
                        clearMaskOnLostFocus: true,
                        removeMaskOnSubmit: true,
                        clearIncomplete: true
                    });
                    innMask.mask(element);
                    break;

                case 'KPP':
                    const kppMask = new Inputmask("9{9}", { clearIncomplete: true });
                    kppMask.mask(element);
                    break;

                case 'email':
                    const emailMask = new Inputmask({
                        mask: "*{1,20}[.*{1,20}][@*{1,20}][.*{1,20}]+",
                        greedy: false,
                        rightAlign: false,
                        clearMaskOnLostFocus: true,
                        removeMaskOnSubmit: true,
                        clearIncomplete: true
                    });
                    emailMask.mask(element);
                    break;

                default:
                    console.warn(`Неизвестный тип маски: ${maskType}`);
                    break;
            }
        });
    }

    validator();
});