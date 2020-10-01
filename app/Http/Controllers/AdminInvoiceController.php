<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;

class AdminInvoiceController extends Controller
{
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 

        if($request->ajax())
        {

            $data = DB::table('invoices as i')
                ->select([
                    'i.id','i.name','i.amount','i.product_name', 'i.paid',
                    'u.name as user_name',
                ])
                ->leftJoin('users as u', 'u.id','=','i.user_id');

            return DataTables::of($data)
                    ->addColumn('action', function($data){
                        $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        if($data->paid == '1'){
                            $button .= '&nbsp;&nbsp;&nbsp;<label class="switch"><input  type="checkbox" id="'.$data->id.'" checked><div class="slider round"><!--ADDED HTML --><span class="on">paid</span><span class="off">unpaid</span><!--END--></div></label>';
                        }else{
                            $button .= '&nbsp;&nbsp;&nbsp;<label class="switch"><input  type="checkbox" id="'.$data->id.'" ><div class="slider round"><!--ADDED HTML --><span class="on">paid</span><span class="off">unpaid</span><!--END--></div></label>';
                        }
                        $button .= '&nbsp;&nbsp;&nbsp;<a type="button" href="invoice/invoice-pdf/'.$data->id.'" target="_blank" name="print" id="'.$data->id.'" class="print btn btn-success btn-sm">Print</a>';
                        
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }


        if(Auth::check()){
        $users = User::orderBy('name', 'ASC')->get();
        return view('admin.invoices.index')->with('users', $users);
        }

        return redirect('/login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $rules = array(
            'name'    =>  'required',
            'product_name'     =>  'required',
            'amount'  => 'required',
            'user_name'  => 'required'
        );

        // dd($request->all());
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // dd($request->all());
        $form_data = array(
            'name'        =>  $request->name,
            'product_name'  =>  $request->product_name,
            'amount' => $request->amount,
            'paid' => '0',
            'user_id' => $request->user_name
            
        );

        
        if(Invoice::create($form_data)){
            return response()->json(['success' => 'Data Added successfully.'], 200);    
        }else{
            return response()->json(['failed' => 'Data is not Added'] , 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        if(request()->ajax())
        {
            $data = Invoice::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Invoice $invoice)
    {
        // dd($request->all());
        
        $rules = array(
            'name'    =>  'required',
            'product_name'     =>  'required',
            'amount'  => 'required',
            'user_name'  => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()], 400);
        }

        $form_data = array(
            'name'        =>  $request->name,
            'product_name'  =>  $request->product_name,
            'amount' => $request->amount,
            'user_id' => $request->user_name
        );

        if(Invoice::whereId($request->hidden_id)->update($form_data)){
            return response()->json(['success' => 'Data is successfully updated'] , 200);    
        }else{
            return response()->json(['failed' => 'Something went wrong.'] , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Invoice::findOrFail($id);
        $data->delete();
    }

    public function pay(Request $request,$id){

        $invoice = Invoice::findOrFail($id);
        $invoice->paid = $request->paid;
        $invoice->save();
        return response()->json([],200);
    }

    public function pdf($id){

        $invoice = Invoice::findOrFail($id);
        // dd($invoice->user->name);
        $pdf = PDF::loadView('invoice_pdf', array('invoice' => $invoice));
        return $pdf->stream('invoice.pdf');
    }
}
