import 'bootstrap'

import Snackbar from 'node-snackbar'
window.showSnackbar = (message) => Snackbar.show({
    text: message,
    pos: 'bottom-right',
    textColor: '#ffffff',
    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--bs-primary'),
    actionTextColor: '#cccccc',
    customClass: 'shadow'
});

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

import Tagify from '@yaireo/tagify'
window.Tagify = Tagify;
