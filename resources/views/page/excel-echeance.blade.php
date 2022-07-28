@extends('adminlte::page')

@section('oui','page')

@section('content_header')
    <h1>Lecteur Excel</h1>
@stop

@section('content')
    <p>Cette page permet de chercher toutes les factures d'un mois en particulier</p>
    <hr class="my-3">
    <form method="post" action="/Read_Excel" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group row">
            <label for="moisEcheance" class="col-sm-1 col-form-label"><strong>Mois</strong></label>
            <div class="col-sm-2.">
                <select name="moisEcheance" type="text" class="form-control" id="moisEcheance">
                    @foreach($aMois as $key => $mois)
                    <option value={{$mois}}>{{$key}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="trieEcheance" class="col-sm-1 col-form-label"><strong>Trier par</strong></label>
            <div class="col-sm-1.">
                <select name="trieEcheance" type="text" class="form-control" id="trieEcheance">
                    <option value=""></option>
                    @foreach($aCOLFACTURE as $key => $nomCol)
                    @if ($key=='A')                        
                    @else
                    <option value={{$key}}>{{$nomCol}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2">
                <input id="value" name="value" type="hidden" value="mois">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </form>
    @if (isset ($sMois) && !empty($sMois))
    <hr class="my-3">
    <span>{{$sMois}}</span>
    @endif
    @if (isset ($sTrie) && !empty($sTrie))
    <span>{{$sTrie}}</span>
    @endif
    <hr class="my-3">
    @if (isset($aFactMois))
    <table class="table table-bordered table-striped">
        <tr>
            @foreach($aFactMois[0] as $key => $value)
            <th> {{$key}} </th>
            @endforeach
        </tr>
        @foreach($aFactMois as $key => $aFacture)
            <tr>
            @foreach($aFacture as $value)
                <td>{{$value}}</td>
            @endforeach
            </tr>
        @endforeach
    </table>
    @endif
@stop