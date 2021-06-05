@extends('errors::minimal', ['hide_home_button' => true])

@section('title', __('Unavailable for legal reasons'))
@section('code', '451')
@section('message', __('This site is not available in :country', [
    'country' => Countries::getOne(GeoIP::getLocation()['iso_code'], app()->getLocale())
]))
