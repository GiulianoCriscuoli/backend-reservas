<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wall;
use App\Models\WallLike;

class WallController extends Controller
{

    public function getAll()
    {

        // O que o método faz?
        // Pega todos os walls, todos os likes no wall
        // e todos os likes do usuário e verifica se tem like

        $status = ['error' => '', 'list' => []];

        $walls = Wall::all();
        $user = auth()->user();

        // passar os likes para dentro do objeto de walls

        foreach ($walls as $wall) {

            // aqui eu crio variáveis da quantidade de like e se o post teve like dentro dos elementos

            $wall['likes'] = 0;
            $wall['liked'] = false;

            // conto os likes de cada  post

            $wallLikes = WallLike::where('id_wall', $wall['id'])
                ->count();

            // verifico quantos likes tem meu em cada post

            $myLikes = WallLike::where('id_wall', $wall['id'])
                ->where('id_user', $user['id'])
                ->count();

            // passo a quantidade para os elementos, cada um deles

            $wall['likes'] = $wallLikes;

            if ($myLikes > 0) {

                // caso haja like meu no post, retorno true para liked

                $wall['liked'] = true;
            }

            // listo todos os posts

            $status['list'] = $walls;
        }

        return $status;
    }

    public function like($id)
    {

        $status = ['error' => '', 'like' => 'Like removido com sucesso!'];

        $wall = Wall::find($id);

        if (!$wall) {

            $status['error'] = 'Este post não foi encontrado';

            return $status;
        }

        $user = auth()->user();

        $wallLikes = WallLike::where('id_user', $user->id)
            ->where('id_wall', $id)
            ->first();

        if (!$wallLikes) {
            $wallLikes = new WallLike;
            $wallLikes->id_wall =  $id;
            $wallLikes->id_user =  $user->id;
            $wallLikes->save();

            return response()->json(['status' => 'Like adicionado com sucesso!'], 201);
        }

        $wallLikes->delete();

        return $status;
    }
}
