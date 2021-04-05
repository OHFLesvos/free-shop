import 'bootstrap'

import Snackbar from 'node-snackbar'
window.showSnackbar = (message) => Snackbar.show({
    text: message,
    pos: 'bottom-right',
    textColor: '#ffffff',
    backgroundColor: '#064477',
    actionTextColor: '#cccccc',    
    customClass: 'shadow'
});
