@extends('errors::minimal', ['hide_home_button' => true])

@section('title', __('Unavailable for legal reasons'))
@section('code', '451')

@php
    $country = Countries::getOne(GeoIP::getLocation()['iso_code'], app()->getLocale());
@endphp

@section('message', "This site is not available in $country.")
