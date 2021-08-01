<?php

namespace App\Console\Commands;

use FTP;
use App\User;
use App\UserDevice;
use Illuminate\Console\Command;
use Lazzard\FtpClient\FtpClient;
use Lazzard\FtpClient\Connection\FtpConnection;
use Lazzard\FtpClient\Config\FtpConfig;

class AddFtpFolderUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zeus:AddFtpFolderUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar los folders via ftp';

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
        $ftp_server   = config('ftp.connections.jcriesgos.host');
        $ftp_username = config('ftp.connections.jcriesgos.username');
        $ftp_userpass = config('ftp.connections.jcriesgos.password');

        ini_set('max_execution_time', 0);
        ini_set('xdebug.max_nesting_level', 2048);

        echo "Conectando.."; 
        $ftp_connection = ftp_connect($ftp_server, 21)
            or die("Could not connect to $ftp_server");

        $login = ftp_login($ftp_connection, $ftp_username, $ftp_userpass);

        ftp_pasv($ftp_connection, true);

        $allList = ftp_nlist($ftp_connection,'JCRiesgos/ASEGURADOS'); 
        echo "\nEscaneando directorios..";
        function stripAccents($string)
        {
            $string = str_replace("'", '', $string);
            return trim($string);
        }

        $array = array();
        foreach ($allList as $key => $dir) {
            $personal_dir = str_replace('JCRiesgos/ASEGURADOS/','',$dir);
            $id = explode('-',$personal_dir);
        
            if(isset($id) && isset($id[1]) && is_numeric($id[1])){
                $user = User::where('id_adminsyf',intval($id[1]))->first();
                $userDevice = UserDevice::where('id_adminsyf',$user->id_adminsyf)->first();
                if(isset($user) && isset($userDevice)){
                    echo "\nEscaneando directorio en: JCRiesgos/ASEGURADOS/{$personal_dir}";
                    $files = ftpRecursiveFileListing($ftp_connection,"JCRiesgos/ASEGURADOS/{$personal_dir}");
                    if(count($files) > 0){
                        echo "\nAgregando folder a: {$user->id_adminsyf}";
                        UserDevice::where('id_adminsyf',$user->id_adminsyf)->update(['folders' => json_encode($files)]);
                    }
                }
            }
        }
        return response()->json(json_encode($files));
    }
}

function ftpRecursiveFileListing($ftpConnection, $path) { 
    $allFiles = array(); 
    $contents = ftp_nlist($ftpConnection, $path); 

    if($contents){
        foreach($contents as $key => $currentFile) { 
            // assuming its a folder if there's no dot in the name 
            $child = [];
            $isDir = ftp_size($ftpConnection, $currentFile) === -1 ? true : false;
            if ($isDir) { 
                $type = 'folder';
                $child = array_merge($child, ftpRecursiveFileListing($ftpConnection, $currentFile));
            }else{
                $type = 'file';
            }

            $label = substr($currentFile, strlen($path) + 1) ? substr($currentFile, strlen($path) + 1): '';
            if($type == 'file'){
                $allFiles[$key] = 
                [
                    'label' => $label,
                    'type'  => $type,
                    'data'  => $path
                ];
            }else{
                $allFiles[$key] = 
                [
                    'label'    => $label,
                    'type'     => $type,
                    'children' => $child
                ];
            }
            
        }
    }
    return $allFiles; 
} 
