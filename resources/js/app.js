require('./bootstrap');

import 'bootstrap'

import bsCustomFileInput from 'bs-custom-file-input'
import $ from 'jquery'

$(function () {
    bsCustomFileInput.init()
})

$(function() {
    $('*[data-href]').on('click', function() {
        window.location = $(this).data('href')
        return false
    })
})
