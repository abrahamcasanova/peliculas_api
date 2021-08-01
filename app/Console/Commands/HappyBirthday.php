<?php

namespace App\Console\Commands;

use App\User;
use App\Mail\Birthday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class HappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zeus:SendMailHappyBirthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para enviar felicitaciones a los clientes';

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
        date_default_timezone_set('America/Mexico_City');

        $users = User::whereMonth('fecha_nacimiento', '=', date('m'))
            ->whereDay('fecha_nacimiento', '=', date('d'))->get();
        $number = 0;
        foreach( $users as $user ) {
            /*if($user->email) {
                //Mail::to('adriana.ortiz@jcriesgos.com')
                Mail::to($user->email)
                ->bcc('abrahamcasanovac@outlook.com')
                ->send(new Birthday($user));
            }*/
            //EJEMPLO LUEGO ELIMINAR
            Mail::to('abrahamcasanovac@outlook.com')
                //Mail::to($user->email)
                //->bcc('abrahamcasanovac@outlook.com')
                ->send(new Birthday($user));
                //return false;
                $number++;
        }
        $this->info("Los mensajes de felicitacion han sido enviados correctamente: {$number}");
    }
}
