<?php

namespace App\Http\Controllers;
use Throwable;


class AdminController extends Controller
{

    public function index()
    {
        return view ('page.Admin-Update');
    }


    public function GitUpdate()
    {
        $sBat='GitPull.bat';
        try{
            exec($sBat);
            return view ('page.Admin-Update')->with('sReussie','Mise-à-jour réussie');
        }catch(Throwable $e){}
        echo 'non';
        return view ('page.Admin-Update')->with('sReussie','Echec de la mise à jour');
    }

}
