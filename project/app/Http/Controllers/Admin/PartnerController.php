<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function datatables()
    {
         $datas = Partner::orderBy('id','desc')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                                ->editColumn('photo', function(Partner $data) {
                                    $photo = $data->photo ? url('assets/images/'.$data->photo):url('assets/images/noimage.png');
                                    return '<img src="' . $photo . '" alt="Image">';
                                })


                                ->addColumn('action', function(Partner $data) {

                                return '<div class="btn-group mb-1">
                                <button type="button" class="btn btn-primary btn-sm btn-rounded dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  '.'Actions' .'
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start">
                                  <a href="' . route('admin.partner.edit',$data->id) . '"  class="dropdown-item">'.__("Edit").'</a>
                                  <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="dropdown-item" data-href="'.  route('admin.partner.delete',$data->id).'">'.__("Delete").'</a>
                                </div>
                              </div>';
                                })
                                ->rawColumns(['photo', 'action'])
                                ->toJson();//--- Returning Json Data To Client Side
    }
    public function index()
    {
        return view('admin.partner.index');
    }
    public function create()
    {
        return view('admin.partner.create');
    }
    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
               'photo'      => 'required|mimes:jpeg,jpg,png,svg',
                ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Partner();
        $input = $request->all();
        if ($file = $request->file('photo'))
         {
            $name = Str::random(8).time().'.'.$file->getClientOriginalExtension();
            $file->move('assets/images',$name);
            $input['photo'] = $name;
        }

        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = 'New Data Added Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function edit($id)
    {
        $data = Partner::findOrFail($id);
        return view('admin.partner.edit',compact('data'));
    }
    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = [
               'photo'      => 'mimes:jpeg,jpg,png,svg',
                ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = Partner::findOrFail($id);
        $input = $request->all();
            if ($file = $request->file('photo'))
            {
                $name = Str::random(8).time().'.'.$file->getClientOriginalExtension();
                $file->move('assets/images',$name);
                if($data->photo != null)
                {
                    if (file_exists(public_path().'/assets/images/'.$data->photo)) {
                        unlink(public_path().'/assets/images/'.$data->photo);
                    }
                }
            $input['photo'] = $name;
            }

        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function destroy($id)
    {
        $data = Partner::findOrFail($id);
        //If Photo Doesn't Exist
        if($data->photo == null){
            $data->delete();
            //--- Redirect Section
            $msg = 'Data Deleted Successfully.';
            return response()->json($msg);
            //--- Redirect Section Ends
        }
        //If Photo Exist
        if (file_exists(public_path().'/assets/images/'.$data->photo)) {
            unlink(public_path().'/assets/images/'.$data->photo);
        }
        $data->delete();
        //--- Redirect Section
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

}
