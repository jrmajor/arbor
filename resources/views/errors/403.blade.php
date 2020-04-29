@extends('errors.layout')

@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden'))
