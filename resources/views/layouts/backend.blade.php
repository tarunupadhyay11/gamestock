@extends('adminlte::page')



@section('footer')
    <div class="float-right d-none d-sm-block">
        <strong>Copyright &copy; {{ date('Y') }} <a href="/">{{ config('app.name') }}</a>.</strong> All rights
        reserved.
    </div>
    {{-- <strong>Copyright &copy; {{ date('Y') }} <a
            href="/">{{ config('app.name') }}</a>.</strong> All rights
    reserved. --}}
@stop
