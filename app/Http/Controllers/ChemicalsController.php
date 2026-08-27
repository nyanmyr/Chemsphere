<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Chemical;
class ChemicalsController extends Controller
{
    function chemicals() {
        $data = DB::table('chemicals')->get();
        $user = Auth::user();
        return view('inventory', ['data'=>$data,'user'=>$user]);
    }

    public function delete($chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail()->delete();
        return redirect()->route('inventory')->with('success', 'Location deleted successfully');
    }
}
