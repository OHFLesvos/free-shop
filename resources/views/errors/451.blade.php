@extends('errors::minimal', ['hide_home_button' => true])

@section('title', __('Unavailable for legal reasons'))
@section('code', '451')

@php
    try {
        $country = Countries::getOne(GeoIP::getLocation()['iso_code'], app()->getLocale());
        $message = "This site is not available in $country. If you are using VPN, please disable it and reload this page.";
    } catch (Monarobase\CountryList\CountryNotFoundException $ex) {
        $message = "We were not able to detect your country. If you are using VPN, please disable it and reload this page.";
    }
@endphp

@section('message', $message)
