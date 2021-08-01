<?php
return array(

    /*
	|--------------------------------------------------------------------------
	| Default FTP Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the FTP connections below you wish
	| to use as your default connection for all ftp work.
	|
	*/

    'default' => 'jcriesgos',

    /*
    |--------------------------------------------------------------------------
    | FTP Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the FTP connections setup for your application.
    |
    */

    'connections' => array(

        'jcriesgos' => array(
            'host'   => 'jcriesgos.dyndns-server.com',
            'port'  => 21,
            'username' => 'Ingjosecarlos',
            'password'   => 'pepe2017',
            'passive'   => true,
            'secure' => false
        ),
    ),
);