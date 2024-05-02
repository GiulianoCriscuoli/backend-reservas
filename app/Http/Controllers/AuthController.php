<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Unit;
use Exception;
use DB;
class AuthController extends Controller
{
    public function unauthorized () {

        return response()->json([
            'error' => 'Não autorizado'

        ], 401);
    }

    public function register (Request $request) {

        try {

            DB::beginTransaction();

            // array para retornar exceções de código

            $status = ['error' => ''];

            // validar campos do formulário de cadastro

            $validator = Validator::make($request->all(), [
                'name'             => 'required',
                'email'            => 'required|email|unique:users,email',
                'cpf'              => 'required|digits:11|unique:users,cpf',
                'password'         => 'required',
                'password_confirm' => 'required|same:password'
            ]);

            // verifica se houve erro

            if ($validator->fails()) {

                $status['error'] = $validator->errors()->first();
                return $status;
            }

            // cria a hash da senha e salva no banco

            $hash = password_hash($request->input('password'), PASSWORD_DEFAULT);

            // User::insert([
            //     'name'     => $request->get('name'),
            //     'email'    => $request->get('email'),
            //     'cpf'      => $request->get('cpf'),
            //     'password' => $hash
            // ]);

            $user = new User();
            $user->name = $request->get('name');
            $user->cpf = $request->get('cpf');
            $user->email = $request->get('email');
            $user->password = $hash;
            $user->save();

            // gera o token de usuário

            $token = auth()->attempt([
                'cpf'      => $request->get('cpf'),
                'password' => $request->get('password')
            ]);

            // se houver erro no token, retorna erro

            if(!$token) {

                $status['error'] = 'Ocorreu um erro interno';
                return $status;
            }

            // Pasdsa o otken e o usuário logado na sessão

            $status['token'] = $token;

            $user = auth()->user();
            $status['user'] = $user;

            // Fazendo o GET de todas as unidades que o usuário da sessão é proprietário

            $properties = Unit::select(['id','name'])
            ->where('id_owner', $user['id'])
            ->get();

            $status['user']['properties'] = $properties->isNotEmpty() ?  $properties : [];

            DB::commit();

            return $status;

        } catch (Exception $e) {

           $status['error'] = $e->getMessage();

           DB::rollBack();

           return response()->json([
                'error' => $status['error']
            ], 500);
        }
    }

    public function login (Request $request){

        try {

            $status = ['error' => ''];

            $validator = Validator::make($request->all(), [
                'cpf'      => 'required|digits:11',
                'password' => 'required'

            ]);

            if ($validator->fails()) {

                $status['error'] = $validator->errors()->first();
                return $status;
            }

            $token = auth()->attempt([
                'cpf'      => $request->get('cpf'),
                'password' => $request->get('password')
            ]);

            // se houver erro no token, retorna erro

            if(!$token) {

                $status['error'] = 'CPF e/ou senha estão errados!';
                return $status;
            }

            // Pasdsa o otken e o usuário logado na sessão

            $status['token'] = $token;

            $user = auth()->user();
            $status['user'] = $user;

            // Fazendo o GET de todas as unidades que o usuário da sessão é proprietário

            $properties = Unit::select(['id','name'])
            ->where('id_owner', $user['id'])
            ->get();

            $status['user']['properties'] = $properties->isNotEmpty() ?  $properties : [];

            DB::commit();

            return $status;

        } catch (Exception $e) {

            $status['error'] = $e->getMessage();

            DB::rollBack();

            return response()->json([
                 'error' => $status['error']
             ], 500);
         }
    }

    public function validateToken() {

        $status = ['error' => ''];

        $user = auth()->user();
        $status['user'] = $user;

        $properties = Unit::select(['id','name'])
        ->where('id_owner', $user['id'])
        ->get();

        $status['user']['properties'] = $properties->isNotEmpty() ?  $properties : [];

        return $status;

    }

    public function logout () {

        $status = ['error' => ''];

        auth()->logout();

        return $status;
    }

}
