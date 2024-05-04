<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function getAll()
    {

        $status = ['error' => '', 'list' => []];

        $docs = Doc::all();

        foreach ($docs as $doc) {

            $doc['fileurl'] = asset('storage/' . $doc['fileurl']);
        }

        $status['list'] = $docs;

        return $status;
    }
}
