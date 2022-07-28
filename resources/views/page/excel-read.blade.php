@extends('adminlte::page')

@section('oui','page')

@section('content_header')
    <h1>Lecteur Excel</h1>
@stop

@section('content')
    <p>Cette page permet de vérifier qu'un fichier contient des doublons de facture, de voir les numéros de facture non payés et toutes les factures pour un chantier</p>

    <hr class="my-3">

    <a class="btn btn-success" href="<?php echo e(route('file-read')); ?>">Factures en double</a>

    <a class="btn btn-success" href="<?php echo e(route('IsPaye')); ?>">N°factures non payées</a>


    <hr class="my-3">

    <form method="post" action="/Read_Excel" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group row">
            <label for="releasedateid" class="col-sm-1 col-form-label"><strong>Chantier</strong></label>
            <div class="col-sm-2">
                <input name="chantier" type="text" class="form-control" id="chantier"
                    placeholder="Nom du chantier">
            </div>
            <div class="form-group row">
                <div class="col-sm-2.">
                    <input id="value" name="value" type="hidden" value="chantier">
                    <button type="submit" class="btn btn-primary">Facture par chantier</button>
                </div>
            </div>
        </div>
    </form>


    <hr class="my-3">
    @if (isset($aDoublon))
    <table class="table table-bordered table-striped">
        <tr>
            @foreach($aDoublon as $key => $value)
            <th> {{$key}} </th>
            @endforeach
        </tr>
         @foreach($aDoublon as $key => $facture)
            @foreach($facture as $value)
            <tr>
                <td>{{$value}}</td>
            </tr>
            @endforeach
        @endforeach
    </table> 
    @endif
    @if (isset($aNonPayee))
    <table class="table table-bordered table-striped">
        <tr>
            @foreach($aNonPayee as $key => $value)
            <th> {{$key}} </th>
            @endforeach
        </tr>
        @foreach($aNonPayee as $key => $facture)
            @foreach($facture as $value)
            <tr>
                <td>{{$value}}</td>
            </tr>
            @endforeach
        @endforeach
    </table>
    @endif
    @if (isset($aChantier))
    <table class="table table-bordered table-striped">
        <tr>
            @foreach($aChantier[0] as $key => $value)
            <th> {{$key}} </th>
            @endforeach
        </tr>
        @foreach($aChantier as $key => $aFacture)
            <tr>
            @foreach($aFacture as $value)
                <td>{{$value}}</td>
            @endforeach
            </tr>
        @endforeach
    </table>
    @endif
@stop