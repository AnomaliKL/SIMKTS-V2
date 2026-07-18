// import './bootstrap';

// import Alpine from 'alpinejs';
// window.Alpine = Alpine;
// Alpine.start();

// // Impor Cleave.js
// import Cleave from 'cleave.js';
// // Impor add-on format nomor telepon internasional
// import 'cleave.js/dist/addons/cleave-phone.i18n';

// // Jadikan global agar bisa dipanggil langsung di file Blade
// window.Cleave = Cleave;


import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Impor JS intl-tel-input saja
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';
window.intlTelInput = intlTelInput;