<?php

namespace App\Console\Commands;

use App\Polices;
use DOMDocument;
use Illuminate\Console\Command;

class AddNewPolices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zeus:AddNewPolices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para insertar nuevas polizas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $params=['nombre'=>'Direccion1', 'password'=>'JCIbarra1*'];
        $defaults = array(
            CURLOPT_URL => 'http://svradm01.adminsyf.com/inc/arrancar.php',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        );
        
        // Initialize cURL object 
        $curlObj = curl_init(); 
        curl_setopt_array($curlObj, ($defaults));
        
        /* setting values to required cURL parameters. 
        CURLOPT_URL is used to set the URL to fetch  
        CURLOPT_RETURNTRANSFER is enabled curl 
        response to be saved in a variable  
        CURLOPT_HEADER enables curl to include 
        protocol header CURLOPT_SSL_VERIFYPEER 
        enables to fetch SSL encrypted HTTPS request.*/
        curl_setopt($curlObj,  CURLOPT_RETURNTRANSFER,  1); 
        curl_setopt($curlObj,  CURLOPT_HEADER,  1); 
        curl_setopt($curlObj,  CURLOPT_SSL_VERIFYPEER,  false); 
        $result = curl_exec($curlObj); 
        
        // Matching the response to extract cookie value 
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', 
                $result,  $match_found); 
        
        $cookies = array(); 
        $finalCookie = null;
        foreach($match_found[1] as $item) { 
            $finalCookie = $item;
            parse_str($item,  $cookie); 
            $cookies = array_merge($cookies,  $cookie); 
        } 
        
        // Printing cookie data 
        print_r( $cookies); 
        
        if($cookies){
            ini_set('max_execution_time', 380);
            $params2 = [
                'bestatus' => 0, 
                'bfecha' => 0,
                'finicio'=>'2015-01-01', 
                'ffin'=> '2040-01-01',
                'bordenar1' => 0,
                'bordenar2' => 1
            ];
            $defaults2 = array(
                CURLOPT_URL => 'http://svradm01.adminsyf.com/polizasqry.php',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $params2,
            );
            
            // Initialize cURL object 
            $curlObj2 = curl_init(); 
            curl_setopt_array($curlObj2, ($defaults2));
            
            /* setting values to required cURL parameters. 
            CURLOPT_URL is used to set the URL to fetch  
            CURLOPT_RETURNTRANSFER is enabled curl 
            response to be saved in a variable  
            CURLOPT_HEADER enables curl to include 
            protocol header CURLOPT_SSL_VERIFYPEER 
            enables to fetch SSL encrypted HTTPS request.*/
            curl_setopt($curlObj2,  CURLOPT_RETURNTRANSFER,  1); 
            curl_setopt($curlObj2,  CURLOPT_HEADER,  1); 
            curl_setopt($curlObj2,  CURLOPT_SSL_VERIFYPEER,  false); 
            curl_setopt($curlObj2, CURLOPT_COOKIE, "{$finalCookie}");
            $result = curl_exec($curlObj2); 
            //echo $result;  

            $DOM = new DOMDocument;
            @$DOM->loadHTML($result);

            foreach($DOM->getElementsByTagName('tr') as $tr) {
                # Show the <a href>
                $poliza = null;
                $endoso = null;
                $ramoSubramo = null;
                $compania = null;
                $contratante = null;
                $comentario = null;
                $fInicial =null;
                $fFinal = null;
                $primaTotal = null;
                $moneda = null;
                $formaPago = null;
                $medioPago = null;
                $polizaAnterior = null;
                $recibos = null;
                $cancelada = null;
                $magicNumber = 7;
                $idAminsyf = null;
                if(isset($tr->firstChild) && $tr->childNodes[$magicNumber]->tagName == 'td'){
                    if($tr->childNodes[$magicNumber]){
                        $stringHref = $tr->childNodes[9]->firstChild->getAttribute('href');
                        if (count(explode('=',$stringHref)) > 1) {
                            $idAminsyf = explode('=',$stringHref)[1];
                        }
                    }
                }

                if($idAminsyf === null){
                    $magicNumber++;

                    if(isset($tr->firstChild) && $tr->childNodes[$magicNumber]->tagName == 'td'){
                        if($tr->childNodes[$magicNumber]){
                            $stringHref = $tr->childNodes[9]->firstChild->getAttribute('href');
                            if (count(explode('=',$stringHref)) > 1) {
                                $idAminsyf = explode('=',$stringHref)[1];
                            }
                        }
                    }
                }

                if($idAminsyf && count($tr->childNodes) > 31){
                    $poliza = $tr->childNodes[1]->nodeValue;
                    $endoso = trim($tr->childNodes[3]->nodeValue);
                    $ramoSubramo = trim($tr->childNodes[5]->nodeValue);
                    $compania = $tr->childNodes[7]->nodeValue;
                    $contratante = $tr->childNodes[9]->nodeValue;
                    $comentario = $tr->childNodes[13]->nodeValue;
                    $fInicial = $tr->childNodes[15]->nodeValue;
                    $fFinal = $tr->childNodes[17]->nodeValue;
                    $primaTotal = floatval(str_replace(',', '', $tr->childNodes[19]->nodeValue));
                    $moneda = trim($tr->childNodes[21]->nodeValue);
                    $formaPago = trim($tr->childNodes[23]->nodeValue);
                    $medioPago = trim($tr->childNodes[25]->nodeValue);
                    $polizaAnterior = trim($tr->childNodes[27]->nodeValue);
                    $recibos = trim($tr->childNodes[29]->nodeValue);
                    $cancelada = trim($tr->childNodes[31]->nodeValue);

                    try {
                        $police = Polices::where('poliza',$poliza)->first();
                        if(!isset($police)){
                            $save = Polices::create([
                                'poliza'           => $poliza,
                                'endoso'           => $endoso,
                                'ramoSubramo'      => $ramoSubramo,
                                'compania'         => $compania,
                                'contratante'      => $contratante,
                                'comentario'       => $comentario,
                                'fInicial'         => $fInicial,
                                'fFinal'           => $fFinal,
                                'primaTotal'       => $primaTotal,
                                'moneda'           => $moneda,
                                'formaPago'        => $formaPago,
                                'medioPago'        => $medioPago,
                                'polizaAnterior'   => $polizaAnterior,
                                'recibos'          => $recibos,
                                'cancelada'        => $cancelada === 'Si' ? true:false,
                                'id_adminsyf'      => intval($idAminsyf),

                            ]);
                            if(!$save){
                                echo 'error';
                            }
                        }
                    } catch (\Throwable $th) {
                        dd($th);
                    }
                }
                
            }
        }
        // Closing curl object instance 
        curl_close($curlObj); 
    }
}
