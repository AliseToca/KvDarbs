import './bootstrap';

import Cookies from '@modules/cookies';
import Accordion from '@modules/accordion';
import Dropdown from '@modules/dropdown';
import Slider from '@modules/slider';
import Tabs from '@modules/tabs';
import Datepicker from '@modules/datepicker';
import FileInput from '@modules/fileInput';

function initializeModules(): void {
    const cookies = new Cookies();
    if (cookies) {
        cookies.init();
    }

    Accordion.init();
    Dropdown.init();
    Tabs.init();
    Slider.init();
    Datepicker.init();
    FileInput.init();
}

initializeModules();
