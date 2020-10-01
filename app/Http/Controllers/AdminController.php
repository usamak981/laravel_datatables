<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
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

            // $data = Sample_data::latest()->with('company')->get();
            $data = DB::table('users as u')
                ->select([
                    'u.id','u.name','u.email',
                    'r.name as role_name','c.name as company_name' 
                ])
                ->leftJoin('roles as r', 'u.role_id','=','r.id')
                ->leftJoin('companies as c', 'c.id','=','u.company_id');
                // dd($data);

            return DataTables::of($data)
                    ->orderColumn('u.name', 'u.name $1')
                    ->addColumn('action', function($data){
                        $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $button;
                    })
                    ->addColumn('check', function($data){
                        return '<input type="checkbox" id="'.$data->id.'" class="check-boxes">';
                    })
                    ->rawColumns(['action','check'])
                    ->make(true);
        }

        if(Auth::check()){
        return view('admin.users.index');
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
            'email'     =>  'required',
            'password'  => 'required'
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
            'email'         =>  $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_name,
            'company_id' => $request->company_name
        );

        
        if(User::create($form_data)){
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
            $data = User::findOrFail($id);
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
    public function update(Request $request,User $user)
    {
        // dd($request->all());
        
        $rules = array(
            'name'        =>  'required',
            'email'         =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()], 400);
        }

        $form_data = array(
            'name'    =>  $request->name,
            'email'     =>  $request->email,
            'role_id'    =>  $request->role_name,
            'company_id' =>  $request->company_name
        );

        if(User::whereId($request->hidden_id)->update($form_data)){
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
        $data = User::findOrFail($id);
        $data->delete();
    }


    public function dashboard(Request $request){
        
        if($request->ajax()){

        if($request->start_date){
            $data = DB::table('users')
            ->select(DB::raw('count(id) as user_count, DATE(created_at) as date' ))
            // ->where('created_at','>=', $request->start_date)
            // ->where('created_at', '<=', $request->end_date)
            ->whereBetween('created_at', [$request->start_date ." 00:00:00", $request->end_date . " 23:59:59"])
            ->groupBy('date')
            ->get();    
        }else{
            $data = DB::table('users')
            ->select(DB::raw('count(id) as user_count, DATE(created_at) as date' ))
            ->groupBy('date')
            ->get();
        }
        return $data;
    }
        // dd($data);

        return view('admin.dashboard');
    
    }


    public function destroyRecords(Request $request){
        
        
        $ids = $request->ids;
        // dd($ids);
        DB::table('users')
                ->whereIn('id', explode(",",$ids))->delete();
    }

}
