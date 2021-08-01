<?php

namespace App\Http\Controllers;

use App\Logs;
use App\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index(){

        $quotes = Quote::with(['logs' => function($query) {
            $query->orderBy('created_at', 'desc')->first();
        }])->get();

        return response()->json($quotes);
    }

    public function store(Request $request){
        DB::beginTransaction();
            
        try {
            $quote = Quote::create($request->all());
    
            DB::commit();

            return response()->json(['quote' => $quote,'message' => 'Usuario guardado correctamente!']);
        }
        catch (GlobalException $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(Request $request){ 
        $request->validate([
            'id'  => 'required',
        ]);

        $quote = Quote::find($request->id);
        if($request->bitacora){
            $quote->status = isset($request->bitacora)  ? $request->bitacora['status']: $quote->status;
            $temp_array = $request->bitacora; 
            $temp_array['quote_id'] = $quote->id;
            $request->merge(['bitacora' => $temp_array]);
            Logs::create($request->bitacora);
        }
        $quote->fill($request->all());
        $quote->save();
        return response()->json(['quote' => $quote,'message' => 'Cotización actualizado correctamente!']);

    }

    public function destroy(Quote $quote){
        return response()->json($quote->delete());
    }
}
