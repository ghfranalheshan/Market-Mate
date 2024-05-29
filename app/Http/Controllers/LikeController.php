<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\UpdateLikeRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LikeController extends Controller
{
    public function addLike(Post $post): \Illuminate\Http\jsonResponse
    {

        $thislike = Like::query()->where('post_id', '=', $post->id)
            ->where('user_id','=',Auth::guard('api')->id())
            ->get()->first();
//        ->value('isLike');

        if ($thislike == null) {
            $like = $post->like()->create(
                [
                    'isLike' => true,
                    'user_id' => Auth::guard('api')->id(),
                ]
            );
            $post->update(
                [
                    'numberOfLikes' => $post->numberOfLikes + 1
                ]
            );
            $result=' like delete';

        }

        else if ($thislike->isLike == true) {

            $thislike->delete();
            $post->update(
                [
                    'numberOfLikes' => $post->numberOfLikes - 1
                ]
            );
            $result=' like delete';
        }


        return Response()->json($result, Response::HTTP_CREATED);
    }
}
