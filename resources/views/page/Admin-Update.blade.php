@extends('adminlte::page')

@section('oui','page')

@section('content_header')
    <h1>Lecteur Excel</h1>
@stop

@section('content')
    <p>Cette page permet de mettre à jour l'application</p>
    <hr class="my-3">
    <a class="btn btn-success" href="<?php echo e(route('AdminUpdate')); ?>">Mise à jour</a>
    <hr class="my-3">
    @if (isset ($sReussie) && !empty($sReussie))
    <span>{{$sReussie}}</span>
   @endif
@stop
