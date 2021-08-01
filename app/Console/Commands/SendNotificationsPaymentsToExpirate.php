<?php

namespace App\Console\Commands;

use DOMDocument;
use App\UserDevice;
use App\Notification;
use Illuminate\Console\Command;

class SendNotificationsPaymentsToExpirate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zeus:paymentsToExpirate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para enviar notificaciones a los usuarios que esta por vencer su recibo de pago';

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
        // URL to fetch cookies 
        $params=['nombre'=>'Direccion1', 'password'=>'JCIbarra1*'];
        $defaults = array(
            CURLOPT_URL => 'http://svradm01.adminsyf.com/inc/arrancar.php',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        );
        
        // Initialize cURL object 
        $curlObj = curl_init(); 
        curl_setopt_array($curlObj, ($defaults));
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
            
            $params2 = ['bestatus' => 2, 'bfecha' => 3,'finicio'=>date('Y-m-01'), 'ffin'=>date('Y-m-t'),'bordenar1' => 0];
            $defaults2 = array(
                CURLOPT_URL => 'http://svradm01.adminsyf.com/recibosqry.php',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $params2,
            );
            
            $curlObj2 = curl_init(); 
            curl_setopt_array($curlObj2, ($defaults2));
            
            curl_setopt($curlObj2,  CURLOPT_RETURNTRANSFER,  1); 
            curl_setopt($curlObj2,  CURLOPT_HEADER,  1); 
            curl_setopt($curlObj2,  CURLOPT_SSL_VERIFYPEER,  false); 
            curl_setopt($curlObj2, CURLOPT_COOKIE, "{$finalCookie}");
            $result = curl_exec($curlObj2); 
            //die($result);  

            $DOM = new DOMDocument;
            @$DOM->loadHTML($result);
            Notification::truncate();
            foreach($DOM->getElementsByTagName('tr') as $tr) {
                # Show the <a href>
                $idAminsyf = null;
                $poliza = null;
                $recibo = null;
                $compania = null;
                $contratante = null;
                $comentario = null;
                $fInicial =null;
                $fFinal = null;
                $diasVigor = null;
                $primaTotal = null;
                $moneda = null;
                $formaPago = null;
                $medioPago = null;
                $pagado = null;
                $magicNumber = 0;
                
                if($tr->childNodes[10 + $magicNumber]->attributes != null && $tr->childNodes[10 + $magicNumber] && $tr->childNodes[10 + $magicNumber]->tagName == 'td'){
                    if($tr->childNodes[10 + $magicNumber]->firstChild){
                        $stringHref = $tr->childNodes[10 + $magicNumber]->firstChild->getAttribute('href');
                        if (count(explode('=',$stringHref)) > 1) {
                            $idAminsyf = explode('=',$stringHref)[1];
                        }
                    }
                }
                
                if($idAminsyf === null){
                    $magicNumber++;
                }
                
                if($tr->childNodes[10 + $magicNumber]->attributes != null && $tr->childNodes[10 + $magicNumber] && $tr->childNodes[10 + $magicNumber]->tagName == 'td'){
                    if($tr->childNodes[10 + $magicNumber]->firstChild){
                        $stringHref = $tr->childNodes[10 + $magicNumber]->firstChild->getAttribute('href');
                        if (count(explode('=',$stringHref)) > 1) {
                            $idAminsyf = explode('=',$stringHref)[1];
                        }
                    }
                }
                
                
                if($idAminsyf && count($tr->childNodes) > 30){
                    $poliza = $tr->childNodes[2 + $magicNumber]->nodeValue;
                    $recibo = $tr->childNodes[0 + $magicNumber]->nodeValue;
                    $compania = $tr->childNodes[8 + $magicNumber]->nodeValue;
                    $contratante = str_replace(array("\r", "\n"), '', trim($tr->childNodes[10 + $magicNumber]->nodeValue));
                    $comentario = trim($tr->childNodes[14 + $magicNumber]->nodeValue);
                    $fInicial = trim($tr->childNodes[16 + $magicNumber]->nodeValue);
                    $fFinal = trim($tr->childNodes[18 + $magicNumber]->nodeValue);
                    $diasVigor = trim($tr->childNodes[20 + $magicNumber]->nodeValue);
                    $primaTotal = trim($tr->childNodes[22 + $magicNumber]->nodeValue);
                    $moneda = trim($tr->childNodes[24 + $magicNumber]->nodeValue);
                    $formaPago = trim($tr->childNodes[28 + $magicNumber]->nodeValue);
                    $medioPago = trim($tr->childNodes[30 + $magicNumber]->nodeValue);
                    $pagado = trim($tr->childNodes[32 + $magicNumber]->nodeValue) == 'No' ? false:true;

                    try {

                        
                        $findNoti = Notification::where('poliza',$poliza)
                            ->where('recibo',$recibo)->first();
                        if(!isset($findNoti)){
                            Notification::create([
                                'id_adminsyf'      => intval($idAminsyf),
                                'poliza'           => $poliza,
                                'recibo'           => $recibo,
                                'compania'         => $compania,
                                'contratante'      => $contratante,
                                'comentario'       => $comentario,
                                'fInicial'         => $fInicial,
                                'fFinal'           => $fFinal,
                                'diasVigor'        => $diasVigor,
                                'primaTotal'       => $primaTotal,
                                'moneda'           => $moneda,
                                'formaPago'        => $formaPago,
                                'medioPago'        => $medioPago,
                                'pagado'           => $pagado,
                            ]);
                            $users = UserDevice::with('user')->where('id_adminsyf',intval($idAminsyf))
                                ->whereNotNull('uuid_device')->get();

                            if($idAminsyf && isset($users)){
                                foreach ($users as $key => $userFind) {
                                
                                    $name = ucwords(strtolower($userFind->user->name));
                                    $message = "Estimado/a {$name}, \n tu póliza con número {$poliza}/{$compania} aun no se encuentra pagada,
                                    este pago es por la cantidad de {$primaTotal}/{$moneda} cubriendo del {$fInicial} hasta el {$fFinal}. \n
                                    Nota: si crees que esto es un error porfavor pide apoyo a nuestros numeros de oficina que se encuentran en el apartado de ayuda de
                                    esta aplicación.
                                    ";
                                    //dd($name,$userFind->uuid_device);
                                    $token = $userFind->uuid_device;
                                    //dd($token,utf8_encode($message));
                                    $url = 'https://fcm.googleapis.com/fcm/send';

                                    $fields = array (
                                            'to' => $token,
                                            'notification' => array (
                                                    "title"         => 'Aviso de pago',
                                                    "body"       => utf8_encode($message),
                                                    "click_action"  => "FLUTTER_NOTIFICATION_CLICK"
                                            )
                                    );
                                    $fields = json_encode ( $fields );
                                    $api_key = env('TOKEN_FCM', "AAAAbO76bZs:APA91bH_bjsjoQnznNhKgMmkz_9DPQhBZZT63xLuvAkPT5aiQSvdAG54Vr3gkLJML78D1wgWQj4UTrv9ydyv8RlIeg7Fqjvxy-hoxnUG3dzbl0VXwGr6Co_DqubDvPNbkeGRNSfmcobd");
                                    //dd($api_key);
                                    $headers = array (
                                            'Authorization: key=' . $api_key,
                                            'Content-Type: application/json'
                                    );
                                    //dd($headers,$fields);
                                    $ch = curl_init ();
                                    curl_setopt ( $ch, CURLOPT_URL, $url );
                                    curl_setopt ( $ch, CURLOPT_POST, true );
                                    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
                                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
                                    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

                                    $result = curl_exec ( $ch );
                                    echo $result;
                                    curl_close ( $ch );
                                }
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