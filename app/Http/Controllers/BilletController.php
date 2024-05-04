<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billet;
use App\Models\Unit;

class BilletController extends Controller
{
    public function getAll(Request $request) {

        $status = ['error' => '', 'list' => []];
        $unit = $request->input('unit');

        if(!$unit) {
            $status['error'] = 'A unidade precisa ser preenchida';
            return $status;
        }

        $user = auth()->user();

        $units = Unit::where('id', $unit)
        ->where('id_owner', $user->id)->get();

        if(count($units) === 0) {
            $status['error'] = 'Nenhuma unidade foi encontrada';
            return $status;
        }

        foreach ($units as $u) {
            $billetsUserLogged = Billet::where('id_unit', $u->id)->get();
            $status['list'] = $billetsUserLogged;
        }

        return $status;
    }
}
