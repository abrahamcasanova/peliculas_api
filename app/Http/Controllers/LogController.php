<?php

namespace App\Http\Controllers;

use App\Logs;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function getBitacoraByQuote($id){
        $logs = Logs::where('quote_id',$id)->with('user')->orderBy('id', 'DESC')->get();
        $randomColors = ['cyan','green','pink','amber','orange','blue','red','grey','primary'];
        $logs->map(function($log) use($randomColors){
            $log->color = $randomColors[array_rand($randomColors, 1)];
            if($log->status === 'Cancelado'){
                $log->color = 'red';
            }
            return $log;
        });
        
        return response()->json($logs);
    }
}
