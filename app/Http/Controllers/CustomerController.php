<?php

namespace App\Http\Controllers;

use Newsletter;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{

    public function getAll(){
        
        //$api = Newsletter::getApi();
        //$result = $api->get('lists');
        $customers = Customer::with('quote')->get();
        $customers = $customers->map(function ($customer) {
            $customer->subscribed = true;
            if (Newsletter::isSubscribed($customer->emails) == false)
                    $customer->subscribed = false;
            return $customer;
        });
        
        //MEJORAR QUE SE OBTENGA EN UN NODO SI ESTA EN MAILCHIMP O NO
        // $result = $this->getCustomersNotInListMailChimp();
        // return response()->json($result);
        return response()->json($customers);  
    }

    public function update(Request $request){ 
        $request->validate([
            'name'  => 'required|string',
            'emails' => 'required',
        ]);

        $request->merge(['type_contact' => $request->type_contact && is_array($request->type_contact) ? $request->type_contact['name']:$request->type_contact]);
        $customer = Customer::find($request->id);
        $result = Newsletter::unsubscribe($customer->emails, 'subscribers');
        $customer->fill($request->all());
        $customer->save();
        Newsletter::subscribeOrUpdate($customer->emails, ['FNAME'=>$customer->name, 'LNAME'=>$customer->last_name]);

        return response()->json(['customer' => $customer,'message' => 'Usuario actualizado correctamente!']);

    }

    public function destroy(Customer $customer){
        Newsletter::unsubscribe($customer->emails, 'subscribers');
        return response()->json($customer->delete());
    }

    public function store(Request $request){
        DB::beginTransaction();
            
        try {
            $request->merge(['type_contact' => $request->type_contact ? $request->type_contact['name']:null]);
            $customer = Customer::create($request->all());

            //Newsletter::subscribe('nuevo@discwoasasrld.com');
            if (Newsletter::isSubscribed($customer->emails) == false)
                Newsletter::subscribe($customer->emails, ['FNAME'=>$customer->name, 'LNAME'=>$customer->last_name]);

            DB::commit();

            return response()->json(['customer' => $customer,'message' => 'Usuario guardado correctamente!']);
        }
        catch (GlobalException $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getCustomersNotInListMailChimp()
    {
        $customers = Customer::where('emails', '!=', null)->chunkById(20, function ($customer) {
            $m = collect();
            foreach ($customer as $c) {
                if (Newsletter::isSubscribed($c['emails']) == false)
                    $m->push($c['email']);
            }
            return $m;
        });

        return response()->json($customers);
    }
}
