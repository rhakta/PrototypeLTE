<?php

namespace App\Http\Controllers;

use App\Models\Num_Facture;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PHPUnit\Framework\Constraint\IsEmpty;
use Throwable;

include_once("constante.php");

class Read_ExcelController extends Controller
{

//    public $oFile=new IOFactory::load(sPATHEXCEL);
    public $aTotal=array();
    public function __construct() {
        $aName=new Num_Facture();        
    }
    public function index()
    {
        return view('page.excel-read')->with('aTotal',$this->aTotal);
    }

    public function edit($id)
    {
        $Num_Facture = Num_Facture::findOrFail($id);
        return Read_ExcelController::index();
    }

    //Retourne la liste de factures en doublons
    public function Read()
    {
        //Ouverture du fichier
        $oReader = new Reader\Xlsx();
        $oFichier= $oReader->load(sPATHEXCEL);
        $this->aTotal = array();
        $aSheetsName=$oFichier->getSheetNames();
        $aOngletsSelectionnes=array();
        foreach($aSheetsName as $SName)
        {
            if (str_contains($SName,sANNEE))
            {
                $aOngletsSelectionnes[]=$SName;
            }
        }
        foreach($aOngletsSelectionnes as $sOnglet)
        {
            $iCompteur=3;
            $iHighestRow = $oFichier->getSheetByName($sOnglet)->getHighestRow();
            $sHighestRow= (string) $iHighestRow;
            $oColonne='C2:C';
            $oColonne.=$sHighestRow;
            $aNum_Facture = $oFichier->getSheetByName($sOnglet)->rangeToArray($oColonne,'Rien',true,true,true);
            foreach($aNum_Facture as $value)
            {
                $this->aTotal[]=$value['C'];
            }
        }
        //recherche des doublons
        $dups = array();
        foreach(array_count_values($this->aTotal) as $val => $c)
        if($c > 1) $dups[] = $val;

        //supprime les "Rien" de la liste des doublons trouvés
        $sASupprimer='Rien';
        unset($dups[array_search($sASupprimer, $dups)]);

        $this->aTotal=array();
        foreach($dups as $value){
            $this->aTotal['Facture en doublon'][] = $value;
        }
        //$this->aTotal['Facture en doublon'][] = $dups;
        return view('page.excel-read')->with('aDoublon',$this->aTotal);
    }

    //Retourne la liste de factures non payées
    public function ReadIsPaye()
    {
        //Ouverture du fichier
        $oReader = new Reader\Xlsx();
        $oSpreadsheet= $oReader->load(sPATHEXCEL);
        // $aReturn = array();
        $this->aTotal = array();
        $aSheetsName=$oSpreadsheet->getSheetNames();
        $aOngletsSelectionnes=array();
        foreach($aSheetsName as $SheetName=>$SName)
        {
            if (str_contains($SName,sANNEE))
            {
                $aOngletsSelectionnes[]=$SName;
            }
        }
        foreach($aOngletsSelectionnes as $sOnglet)
        {
            $iCompteur=3;
            $iHighestRow = $oSpreadsheet->getSheetByName($sOnglet)->getHighestRow();
            $sHighestRow= (string) $iHighestRow;
            $oColonne='C2:C';
            $oColonne.=$sHighestRow;
            $oColonneP='H2:H';
            $oColonneP.=$sHighestRow;
            $aNum_Facture = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray($oColonne,'Rien',true,true,true);
            $aIs_Paye = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray($oColonneP,'Rien',true,true,true);
            foreach($aNum_Facture as $key => $value)
            {
                if (strcmp($aIs_Paye[$iCompteur]['H'],'Rien')==0 && strcmp($value['C'],'Rien')==0)
                {

                }
                else if (strcmp( $aIs_Paye[$iCompteur]['H'],'Rien' )==0)
                {
                    $this->aTotal['Facture impayee'][] = $value['C'];
                }
                $iCompteur++;
            }
        }
        return view('page.excel-read')->with('aNonPayee',$this->aTotal);
    }
    
    //Retourne la liste des factures pour un chantier
    public function store(Request $request)
    {
        $this->aTotal = array();
        $oReader = new Reader\Xlsx();
        $oSpreadsheet= $oReader->load(sPATHEXCEL);
        $aSheetsName=$oSpreadsheet->getSheetNames();
        $aOngletsSelectionnes=array();
        switch (request('value')){
            case 'chantier':
                foreach($aSheetsName as $SheetName=>$SName)
                {
                    if (str_contains($SName,sANNEE))
                    {
                        $aOngletsSelectionnes[]=$SName;
                    }
                }
                $parse=0;
                foreach($aOngletsSelectionnes as $sOnglet)
                {
                    $iCompteur=2;
                    $iHighestRow = $oSpreadsheet->getSheetByName($sOnglet)->getHighestRow();
                    $sHighestRow= (string) $iHighestRow;
                    $sColE='E2:E';
                    $sColE.=$sHighestRow;
                    $aNomChan = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray($sColE,'Rien',false,false,true);
                    foreach($aNomChan as $sNomChan)
                    {
                        if (strcmp(trim($sNomChan['E']),trim(strtoupper(request('chantier'))))==0)
                        {
                            $aFactures = $oSpreadsheet->getSheetByName($sOnglet)->rangeToArray('B'.(string)$iCompteur.':G'.(string)$iCompteur,'Rien',false,false,true);
                            foreach($aFactures as $aDetailFacture)
                            {
                                foreach ($aDetailFacture as $key => $value)
                                {
                                    $this->aTotal[$parse][aCOLFACTURE[$key]] = $value;
                                }
                            }
                            $parse++;
                        }
                        $iCompteur++;
                    }
                }      
                return view('page.excel-read')->with('aChantier',$this->aTotal)->with('aMois',aMois);      
            ;
            case 'mois':
                $oFiles=array();
                $oFiles[]=$oSpreadsheet;
                try{
                $oSpreadsheet2= $oReader->load(sPATHSANSANNEE.(string) (intval(sANNEE)+1).".xlsx");
                $oFiles[]=$oSpreadsheet2;
                } catch(Throwable $e){
                }
                try{
                    $oSpreadsheet3= $oReader->load(sPATHSANSANNEE.(string) (intval(sANNEE)-1).".xlsx");
                    $oFiles[]=$oSpreadsheet3;
                    } catch(Throwable $e){
                }
                foreach($oFiles as $spreadsheet)
                {
                    $aSheetsName=$spreadsheet->getSheetNames();
                    $aOngletsSelectionnes=array();
                    foreach($aSheetsName as $SheetName=>$SName)
                    {
                        if (str_contains($SName,request('moisEcheance')))
                        {
                            $aOngletsSelectionnes[]=$SName;
                        }
                    }
                    if (empty($aOngletsSelectionnes))
                    {
                        return view('page.excel-echeance')->with('aMois',aMois)
                                                        ->with('sMois',"Le fichier ne possède pas le mois ".request('moisEcheance'))
                                                        ->with('aCOLFACTURE',aCOLFACTURE);
                    }
                    $parse=0;
                    foreach($aOngletsSelectionnes as $sOnglet)
                    {
                        $iCompteur=2;
                        $iHighestRow = $spreadsheet->getSheetByName($sOnglet)->getHighestRow();
                        $sHighestRow= (string) $iHighestRow;
                        $sColE='E2:E';
                        $sColE.=$sHighestRow;
                        $aNomChan = $spreadsheet->getSheetByName($sOnglet)->rangeToArray($sColE,'Rien',true,true,true);
                        foreach($aNomChan as $sNomChan)
                        {
                            $aFactures = $spreadsheet->getSheetByName($sOnglet)->rangeToArray('B'.(string)$iCompteur.':G'.(string)$iCompteur,'Rien',true,true,true);
                            foreach($aFactures as $aDetailFacture)
                            {
                                if(! strcmp( $aDetailFacture['B'],'Rien' ) == 0)
                                {
                                    foreach ($aDetailFacture as $key => $value)
                                    {
                                        $this->aTotal[$parse][aCOLFACTURE[$key]] = $value;
                                    }
                                }
                            }
                            $parse++;
                            $iCompteur++;
                        }
                    }
                }
                if (!request('trieEcheance')==0)
                {
                    $colonne = array_column($this->aTotal,aCOLFACTURE[request('trieEcheance')]);
                    array_multisort($colonne,SORT_ASC,$this->aTotal);
                    return view('page.excel-echeance')->with('aCOLFACTURE',aCOLFACTURE)
                                                      ->with('aFactMois',$this->aTotal)
                                                      ->with('sTrie'," triées par ".aCOLFACTURE[request('trieEcheance')])
                                                      ->with('aMois',aMois)
                                                      ->with('sMois',"Voici les factures du mois numéro ".request('moisEcheance'));
                }
                return view('page.excel-echeance')->with('aCOLFACTURE',aCOLFACTURE)
                                                  ->with('aFactMois',$this->aTotal)
                                                  ->with('aMois',aMois)
                                                  ->with('sMois',"Voici les factures du mois numéro ".request('moisEcheance'));
            ;
        }
    }
}