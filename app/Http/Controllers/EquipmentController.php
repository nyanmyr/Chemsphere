<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function equipment()
    {
        $data = DB::table('equipment')->get();
        $user = Auth::user();

        return view('equipment', ['data' => $data, 'user' => $user]);
    }

    public function delete($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail()->delete();

        return redirect()->route('equipment')->with('success', 'Equipment deleted successfully');
    }

}
