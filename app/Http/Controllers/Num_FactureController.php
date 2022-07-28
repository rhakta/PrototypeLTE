<?php

namespace App\Http\Controllers;

use App\Models\Num_Facture;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use App\Http\Controllers\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

include_once ("constante.php");

class Num_FactureController extends Controller
{
    public $aName;
    public $sVerif="";

    public $aFournFact;
    public function __construct() {
        $aName=new Num_Facture();        
    }
        public function index()
    {
        return view('page.excel-edit')->with ('aCat',aCat) ->with('sVerif',$this->sVerif);
    } 
    public function getContentExcel()
    {
        $aTmpFournFact=array();

        $reader = new Reader\Xlsx();
        $oFichier= $reader->load(sPATHEXCEL);
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
            $iHighestRow = $oFichier->getSheetByName($sOnglet)->getHighestRow();
            $sHighestRow= (string) $iHighestRow;
            $oColonne='B2:C';
            $oColonne.=$sHighestRow;
            $aNum_Facture = $oFichier->getSheetByName($sOnglet)->rangeToArray($oColonne,'Rien',true,true,true);
            foreach($aNum_Facture as $value){
                $aTmpFournFact[strtoupper(trim($value['B']))][]=trim($value['C']);
            }
        }
        return ($aTmpFournFact);
    }
    public function edit($id)
    {
        $Num_Facture = Num_Facture::findOrFail($id);
        return Num_FactureController::index();
    }


    public function store(Request $request)
    {
        $this->sVerif='';
        $this->aFournFact=$this->getContentExcel();
        if (!request('facture')=='')
        {
            try{
                if(in_array(trim(request('facture')), $this->aFournFact[strtoupper(trim(request('fournisseur')))]))
                {
                    $this->sVerif='Erreur : le numéro de facture existe déjà';
                    return view('page.excel-edit')->with ('aCat',aCat) ->with('sVerif',$this->sVerif);
                }
            }catch(Throwable $e){}
        }

        //Ouverture du fichier
        $reader = new Reader\Xlsx();
        $oFichier= $reader->load(sPATHEXCEL);

        //Onglet actif
        $aSheetsName=$oFichier->getSheetNames();
        $aDate=explode('/',request('date_echeance'));
        $sMois=$aDate[1];
        $sOngletSelectionne='';
        foreach($aSheetsName as $SheetName=>$SName)
        {
            if (str_contains($SName,$sMois))
            {
                $sOngletSelectionne=$SName;
                break;
            }
        }

        if ($sOngletSelectionne=="")
        {
            $sOngletSelectionne=$sMois."-".sANNEE;
            $sheet =clone $oFichier->getSheet(0);
            $sheet->setTitle($sOngletSelectionne);
            $oFichier->addSheet($sheet);   
        }

        $oOnglet = $oFichier->getSheetByName($sOngletSelectionne);
        $iMaxLigne=$oFichier->getSheetByName($sOngletSelectionne)->getHighestDataRow();
        $iInsertLigne=1;
        $c=1;
        $aCategorieInFile=$oOnglet->rangeToArray('A1:A'.(string)$iMaxLigne,'Rien',false,false,true);
        $aFournisseurInFile=$oOnglet->rangeToArray('B1:B'.(string)$iMaxLigne,'Rien',false,false,true);
        $bBonCat=false;
        //scan de la colonne Categorie pour insertion dans le tableau
        foreach ($aCategorieInFile as $key => $value)
        {
            if (strcmp (request('categorieFacture'),trim($value['A'])) == 0  )  //recherche de la bonne catégorie où mettre la facture
            {
                $iInsertLigne=$c;
                $bBonCat=true;
            }
            elseif (strcmp('Rien',trim($value['A'])) == 0  )
            {
                //ne rien faire
            }
            else
            {
                $bBonCat=false;
            }
            if ($bBonCat)
            {
                if( strcmp('Rien',trim($aFournisseurInFile[$c]['B']))==0 )
                {
                    $iInsertLigne=$c;
                    break;
                }
            }
            $c++;
        }
        //Edit du fichier sur l'onglet actif
        $oOnglet->insertNewRowBefore($iInsertLigne+1,1);
        for ($i=1;$i<=5;$i++)
        {
            $tmp_S=aNOMCOL[$i];
            $tmp_S.=(string) $iInsertLigne;
            if($i==1)   //Si on remplit le fournisseur
            {
                $oOnglet->setCellValue($tmp_S,strtoupper( request('fournisseur')) );
            }
            elseif($i==2) //Si on remplit le numéro de facture
            {
                $oOnglet->setCellValue($tmp_S,request('facture'));
            }
            elseif($i==3) //Si on remplit le montant
            {
                $oOnglet->setCellValue($tmp_S,request('montant'));
            }
            elseif($i==4)   //Si on remplit le chantier
            {
                $oOnglet->setCellValue($tmp_S,strtoupper( request('chantier') ));
            }
            elseif ($i==5)      //Si on remplit la date d'échéance
            {
                $oOnglet->setCellValue($tmp_S,request('date_echeance').'/'.sANNEE);
            }
        }

        //Ecriture et sauvegarde du fichier
        $writer = new Xlsx($oFichier);
        try
        {
            $writer->setPreCalculateFormulas(false);
            $writer->save(sPATHEXCEL);
        }   
        catch (Throwable $e)
        {
            echo 'toto';
            print_r($e);
        }
        $this->sVerif='Facture stockée !';
        return view('page.excel-edit')->with ('aCat',aCat) ->with('sBon',$this->sVerif);
    }
}