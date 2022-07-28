@extends('adminlte::page')

@section('oui','page')

@section('content_header')
    <h1>Editeur Excel</h1>
@stop

@section('content')
    <p>Cette page permet l'ajout de facture sur un fichier Excel</p>

    <hr class="my-4">

    <h2>Ajouter une facture</h2>

    <form method="post" action="/Num_Facture" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class='form-group row'>
          <label for="titleid" class="col-sm-2 col-form-label"><strong>Catégorie de la facture (obligatoire)</strong></label>
          <div class="col-sm-1.">
            <select   name="categorieFacture" required="" class="form-control custom-select">
              <option value=""></option>
              @foreach($aCat as $Cat)
                <option value="{{$Cat}}">{{$Cat}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
            <label for="fournisseur" class="col-sm-2 col-form-label"><strong>Fournisseur (obligatoire)</strong></label>
            <div class="col-sm-3.">
                <input name="fournisseur" required="" type="text" class="form-control" id="fournisseur"
                       placeholder="Fournisseur de la facture">
            </div>
        </div>
        <div class="form-group row">
            <label for="facture" class="col-sm-2 col-form-label"><strong>N°Facture</strong></label>
            <div class="col-sm-3.">
                <input name="facture" type="text" class="form-control" id="facture"
                       placeholder="N° de la facture">
                       @if (isset ($sVerif) && !empty($sVerif))
                       <span style="color: red;">{{$sVerif}}</span>
                      @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="montant" class="col-sm-2 col-form-label"><strong>Montant</strong></label>
            <div class="col-sm-3.">
                <input name="montant" type="text" id="montant" class="form-control" 
                        placeholder="Montant de la facture">
            </div>
        </div>
        <div class="form-group row">
          <label for="chantier" class="col-sm-2 col-form-label"><strong>Chantier</strong></label>
          <div class="col-sm-3.">
              <input name="chantier" type="text" class="form-control" id="chantier"
                     placeholder="Nom du chantier">
          </div>
        </div>
        <div class="form-group row">
          <label for="date_echeance" class="col-sm-2 col-form-label"><strong>Date d'échéance (obligatoire)</strong></label>
          <div class="col-sm-3,5">
            <input name="date_echeance" required="" type="text" class="form-control" id="date_echeance"
                   placeholder="Sous format jj/mm">
          </div>
        </div>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-3.">
                <button type="submit" class="btn btn-primary">Stocker Facture</button>
            </div>
        </div>
    </form>
    @if (isset ($sBon) && !empty($sBon))
    <span style="color: green;">{{$sBon}}</span>
   @endif
</body>
</div>
@stop