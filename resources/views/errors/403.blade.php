@extends('errors.layout')

@section('code', '403')
@section('message', $exception->getMessage() ?: __('errors.e403'))
