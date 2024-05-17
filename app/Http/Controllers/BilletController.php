<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billet;
use App\Models\Unit;

class BilletController extends Controller
{
    public function getAll(Request $request) {

        $status = ['error' => '', 'list' => []];

        // passo por get a unidade 

        $unit = $request->input('unit');

        // verifico se ela existe

        if(!$unit) {
            $status['error'] = 'A unidade precisa ser preenchida';
            return $status;
        }

        $user = auth()->user();

        // consulto as unidades do usuário logado

        $units = Unit::where('id', $unit)
        ->where('id_owner', $user->id)->get();

        if(count($units) === 0) {
            $status['error'] = 'Nenhuma unidade foi encontrada';
            return $status;
        }

        // caso encontre, pego os boletos da unidade 

        foreach ($units as $u) {
            $billetsUserLogged = Billet::where('id_unit', $u->id)->get();
            $status['list'] = $billetsUserLogged;
        }

        return $status;
    }
}
