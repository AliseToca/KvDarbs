import flatpickr from 'flatpickr';
import { Latvian } from 'flatpickr/dist/l10n/lv.js';
import { CustomLocale } from 'flatpickr/dist/types/locale';

class Datepicker {
    private readonly datepickers: NodeListOf<Element>;
    private readonly highlightedDates: number[];

    constructor(highlightedDates: (Date | string)[] = []) {
        this.datepickers = document.querySelectorAll('[data-flatpickr]');
        this.highlightedDates = highlightedDates.map((date) => new Date(date).setHours(0, 0, 0, 0));
        this.initializeDatepickers();
    }

    private initializeDatepickers(): void {
        const customLocale: CustomLocale = {
            ...Latvian,
            weekdays: {
                shorthand: Latvian.weekdays.longhand.map((day) => day.charAt(0)) as [
                    string,
                    string,
                    string,
                    string,
                    string,
                    string,
                    string,
                ],
                longhand: Latvian.weekdays.longhand,
            },
            months: Latvian.months,
        };

        this.datepickers.forEach((datepickerElement) => {
            const options = {
                locale: customLocale,
                altInput: true,
                altFormat: 'd.m.Y',
                dateFormat: 'Y-m-d',
                onDayCreate: (dObj, dStr, fp, dayElem) => {
                    const date = dayElem.dateObj.setHours(0, 0, 0, 0);
                    if (this.highlightedDates.includes(date)) {
                        dayElem.className += ' has-trip';
                    }
                },
                ...(datepickerElement.hasAttribute('inline') && {
                    appendTo: datepickerElement.parentElement?.parentElement as HTMLElement,
                    inline: true,
                }),
                ...(datepickerElement.hasAttribute('mode') && {
                    mode: datepickerElement.getAttribute('mode') as 'single' | 'multiple' | 'range' | 'time',
                }),
            };

            flatpickr(datepickerElement as HTMLElement, options);
        });
    }

    static init(highlightedDates: (Date | string)[] = []): void {
        new Datepicker(highlightedDates);
    }
}

export default Datepicker;
