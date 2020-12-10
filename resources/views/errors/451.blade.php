@extends('errors::minimal')

@section('title', __('Unavailable for legal reasons'))
@section('code', '451')
@section('message', 'This site is not available in ' . geoip()->getLocation()['country'])
