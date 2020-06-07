@extends('errors.layout')

@section('code', '503')
@section('message', $exception->getMessage() ?: __('errors.503'))
