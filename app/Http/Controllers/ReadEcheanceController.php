<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Num_Facture;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;

include_once("constante.php");

class ReadEcheanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $aTotal=array();
    public function index()
    {
        return view('page.excel-echeance')->with('aMois',aMois)->with('aCOLFACTURE',aCOLFACTURE);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Ouverture du fichier
        $oReader = new Reader\Xlsx();
        $oSpreadsheet= $oReader->load(sPATHEXCEL);
        $this->aTotal = array();

        $aSheetsName=$oSpreadsheet->getSheetNames();
        $aOngletsSelectionnes=array();
        foreach($aSheetsName as $SheetName=>$SName)
        {
            if (str_contains($SName,request('moisEcheance')))
            {
                $aOngletsSelectionnes[]=$SName;
            }
        }
        $parse=0;
        foreach($aOngletsSelectionnes as $sOnglet)
        {
            $iCompteur=3;
            $iHighestRow = $oSpreadsheet->getSheetByName($sOnglet)->getHighestRow();
            $sHighestRow= (string) $iHighestRow;
            $sColE='E3:E';
            $sColE.=$sHighestRow;
            $aNomChan = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray($sColE,'Rien',true,true,true);
            foreach($aNomChan as $sNomChan)
            {
                $aFactures = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray('B'.(string)$iCompteur.':G'.(string)$iCompteur,'Rien',true,true,true);
                foreach($aFactures as $aDetailFacture)
                {
                    foreach ($aDetailFacture as $key => $value)
                    {
                        $this->aTotal[$parse][aCOLFACTURE[$key]] = $value;
                    }
                }
                $parse++;
                $iCompteur++;
            }
        }
        return view('page.excel-echeance')->with('aChantier',$this->aTotal)->with('aMois',aMois)->with('aCOLFACTURE',aCOLFACTURE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
