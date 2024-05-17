<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request) {

        try {

            $status =  ['error' => ''];

            $property = $request->input('property');

            if(!$property) {

                $status['error'] = 'Preopriedade não foi encontrada';

                return response()->json([
                    'status' => $status
                ], 500);
            }

            $user = auth()->user();

            $unit = Unit::where('id', $property)
            ->where('id_owner', $user['id'])
            ->count();

            if($unit <= 0) {

                $status['error'] = 'Esta unidade não é sua';

                return response()->json([
                    'status' => $status
                ], 500);
            }

            $warnings = Warning::where('id_unit', $property)
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

            foreach($warnings as $warning) {

                $warning['datecreated'] = date("d/m/y", strtotime($warning['datecreated']));
                $photoList = [];
                $photos = explode(',', $warning['photos']);

                foreach($photos as $photo) {
                    if(!empty($photo)) {
                        $photoList[] = asset('storage/'. $photo);

                        $warning['photos'] = $photoList;
                    }
                }
            }

            return response()->json([
                'status'   => $status,
                'warnings' => $warnings
            ], 200);

        } catch (Exception $e) {

            $status['error'] = $e->getMessage();

            return response()->json([
                'error' => $status['error']
            ], 500);
        }
    }

    public function addWarningFile (Request $request) {

        try {

            $status = ['error' => ''];

            // valida os dados enviados pela request

            $validator = Validator::make($request->all(), [
                'photo' => 'required|file|mimes:jpg,png,jpeg'
            ]);

            // primeiro valida se não tem algum erro no validador

            if($validator->fails()) {

                $status['error'] = $validator->errors()->first();

                return response()->json([
                    'error' => $status['error']
                ], 500);
            }

            // Adiciona arquivo no storage

            $file = $request->file('photo')->store('public');
            $array['photo'] = asset(Storage::url($file));

            return response()->json([
                'status'   => $status,
                'photo'    => $array['photo']
            ], 200);

        } catch (Exception $e) {

            $status['error'] = $e->getMessage();

            return response()->json([
                'error' => $status['error']
            ], 500);
        }
    }

    public function setWarnings(Request $request) {

        try {

            $status = ['error' => ''];

            $validator = Validator::make($request->all(), [
                'title'    => 'required',
                'property' => 'required'
            ]);

            if($validator->fails()) {
                $status['error'] = $validator->errors()->first();

                return response()->json([
                    'error' => $status['error']
                ], 500);
            }

            $list = $request->input('list');

            $warning = new Warning;
            $warning->title       = $request->input('title');
            $warning->id_unit     = $request->input('property');
            $warning->photos      = '';
            $warning->status      = 'IN_REVIEW';
            $warning->datecreated = date('y-m-d');

            if($list && is_array($list)) {
                $photos = [];

                foreach($list as $item) {
                    $url = explode("\\", $item);
                    $photos[] = end($url);
                }

                $warning->photos = implode(',', $photos);
            }

            $warning->save();

            return response()->json([
                'status'   => $status,
            ], 201);

        } catch (Exception $e) {

            $status['error'] = $e->getMessage();

            return response()->json([
                'error' => $status['error']
            ], 500);
        }
    }
}
