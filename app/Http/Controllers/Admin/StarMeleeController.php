<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportStarMelee;
use App\Http\Controllers\Controller;
use App\Imports\ImportStarMelee;
use App\Models\StarMelee;
use App\Models\StarMeleeInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StarMeleeController extends Controller
{
    public function index()
    {
        $StarMelee = StarMelee::all();
        return response()->json(['success' => true, 'data' => ['result' => $StarMelee, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'shape' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $data['shape'] = $request->shape;
        $data['size'] = $request->size;
        $data['sieve'] = $request->sieve;
        $data['carat'] = $request->carat;
        $data['def_vvs_vs'] = $request->def_vvs_vs;
        $data['def_vs_si'] = $request->def_vs_si;
        $data['fg_vvs_vs'] = $request->fg_vvs_vs;
        $data['fg_vs_si'] = $request->fg_vs_si;
        $data['pink_vvs_vs_si1'] = $request->pink_vvs_vs_si1;
        $data['yellow_vvs_vs_si1'] = $request->yellow_vvs_vs_si1;
        $data['blue_vvs_vs_si1'] = $request->blue_vvs_vs_si1;
        // $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $StarMelee = StarMelee::create($data);
        return response()->json(['success' => true, 'data' => ['result' => $StarMelee, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'shape' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $StarMelee = StarMelee::find($id);
        if($StarMelee) {
            $data['shape'] = $request->shape;
            $data['size'] = $request->size;
            $data['sieve'] = $request->sieve;
            $data['carat'] = $request->carat;
            $data['def_vvs_vs'] = $request->def_vvs_vs;
            $data['def_vs_si'] = $request->def_vs_si;
            $data['fg_vs_si'] = $request->fg_vs_si;
            $data['pink_vvs_vs_si1'] = $request->pink_vvs_vs_si1;
            $data['yellow_vvs_vs_si1'] = $request->yellow_vvs_vs_si1;
            $data['blue_vvs_vs_si1'] = $request->blue_vvs_vs_si1;
            $StarMelee = $StarMelee->update($data);
            return response()->json(['success' => true, 'data' => ['result' => $StarMelee, 'message' => 'successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Star Melee not found']], 500);
    }

    public function delete($id)
    {
        $StarMelee = StarMelee::find($id);
        if($StarMelee){
            $StarMelee->delete();
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Star Melee deleted successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Star Melee not found']], 500);
        }
    }

    public function status(Request $request, $id)
    {
        $StarMelee = StarMelee::find($id);
        if($StarMelee){
            $StarMelee->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $StarMelee, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Star Melee not found']], 500);
        }
    }

    public function import(Request $request)
    {
        Excel::import(new ImportStarMelee(), $request->file('excel')->store('files'));
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'successfully.']], 200);
    }

    public function inquiry()
    {
        $inquiry = StarMeleeInquiry::all();
        return response()->json(['success' => true, 'data' => ['result' => $inquiry, 'message' => 'successfully.']], 200);
    }

    public function export()
    {
        $name = 'DD_SM_'.date('dmY_His').'.xlsx';
        return Excel::download(new ExportStarMelee(), $name);
    }
}
