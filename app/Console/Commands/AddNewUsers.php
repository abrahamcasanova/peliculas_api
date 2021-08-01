<?php

namespace App\Console\Commands;

use App\User;
use DOMDocument;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddNewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zeus:AddNewUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para agregar nuevos usuarios a la bd interna';

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
            $params2 = ['finicio'=>'2000-12-27', 'ffin'=>'2080-12-27','bordenar1' => 0];
            $defaults2 = array(
                CURLOPT_URL => 'http://svradm01.adminsyf.com/personasqry.php',
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

            $DOM = new DOMDocument;
            @$DOM->loadHTML($result);

            foreach($DOM->getElementsByTagName('tr') as $tr) {
                # Show the <a href>
                $id = null;
                $mail = null;
                $name = null;
                $magicNumber = 0;
                //dd($tr->childNodes[1]->tagName);
                if($tr->childNodes[0 + $magicNumber]->attributes != null && $tr->childNodes[0 + $magicNumber]->tagName == 'td'){
                    if($tr->childNodes[0 + $magicNumber] && isset($tr->childNodes[0 + $magicNumber]->firstChild)){
                        $stringHref = $tr->childNodes[0 + $magicNumber]->firstChild->getAttribute('href');
                        if (count(explode('=',$stringHref)) > 1) {
                            $id = explode('=',$stringHref)[1];
                        }
                    }
                }
                
                if($id === null){
                    $magicNumber++;

                    if($tr->childNodes[0 + $magicNumber]->attributes != null && $tr->childNodes[0 + $magicNumber]->tagName == 'td'){
                        if($tr->childNodes[0 + $magicNumber] && isset($tr->childNodes[0 + $magicNumber]->firstChild)){
                            $stringHref = $tr->childNodes[0 + $magicNumber]->firstChild->getAttribute('href');
                            if (count(explode('=',$stringHref)) > 1) {
                                $id = explode('=',$stringHref)[1];
                            }
                        }
                    }

                }
                


                if($id && count($tr->childNodes) > 8){
                    $magicNumber--;
                    $email = $tr->childNodes[7 + $magicNumber]->nodeValue != '' ? $tr->childNodes[7 + $magicNumber]->nodeValue:null; 
                    $name = $tr->childNodes[1 + $magicNumber]->nodeValue != '' ? $tr->childNodes[1 + $magicNumber]->nodeValue:null;
                    $rfc = $tr->childNodes[5 + $magicNumber]->nodeValue != '' ? $tr->childNodes[5 + $magicNumber]->nodeValue:null;
                    $fecha_nacimiento = $tr->childNodes[11 + $magicNumber]->nodeValue != '' ? $tr->childNodes[11 + $magicNumber]->nodeValue:null;

                    
                    try {
                        $userFind = User::where('id_adminsyf',$id)->first();
                        $fecha_nacimiento = $fecha_nacimiento != '' ? Carbon::parse($fecha_nacimiento)->format('Y-m-d'):null;
                        //dd($userFind);
                        if(!isset($userFind)){
                            $save = User::create([
                                'id_adminsyf'        => intval($id),
                                'name'              => $name == null ? '':str_replace(array("\r", "\n"), '', trim($name)),
                                'email'             => null,
                                'rfc'               => $rfc,
                                'fecha_nacimiento'  => $fecha_nacimiento,
                                'email_adminsyf'     => $email == null ? '':$email,
                                'password'          => Hash::make("admisfy_{$id}")
                            ]);
                            if(!$save){
                                echo 'error';
                            }
                        }else{
                            $userFind->name = $name == null ? '':str_replace(array("\r", "\n"), '', trim($name));
                            $userFind->rfc = $rfc;
                            $userFind->email_adminsyf = $email == null ? '':$email;
                            $userFind->fecha_nacimiento = $fecha_nacimiento;
                            $userFind->save();
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