<?php

namespace App\Http\Controllers;


use PhpOffice\PhpSpreadsheet\IOFactory;
include_once ("constante.php");

class Num_FactureController extends Controller
{
    public function Load()
    {
        return IOFactory::load(sPATHEXCEL."\Copie de TRESORERIE4-2021.xlsx");
    }

}
