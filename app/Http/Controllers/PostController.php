<?php

namespace App\Http\Controllers;

use App\Http\Traits\Images;
use App\Models\FollowUpRequest;
use App\Models\Like;
use App\Models\Market;
use App\Models\Photo;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\Notifications;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $following = FollowUpRequest::query()->where('user_id', '=', Auth::guard('api')->id())
            ->where('request_status', '=', 'accepted')->value('market_id');
        if ($following == null) {
            return response()->json('there are not any posts to show, follow markets to see their posts');
        } else {

            $posts = [];
            foreach ($following as $item) {
                $posts = Post::query()->with('photo')->where('market_id', '=', $item)->get();

            }
            if ($posts == null) {
                return response()->json('there are not any post to show , follow markets to see their posts');
            }
            $language = $request->headers->get('Language');

            $translatedPosts = $posts->filter(function ($post) use ($language) {
                return $post->hasTranslation('text', $language);
            });

            foreach ($translatedPosts as $post) {
                $isLiked = Like::query()->where('user_id', '=', Auth::guard('api')->id())
                    ->where('post_id', '=', $post->id)->value('isLike');
                if ($isLiked == null || $isLiked == false) {
                    $post['isLiked'] = false;
                } else {
                    $post['isLiked'] = true;
                }

                $post['market'] = Market::query()->find($post->market_id);
            }
            return response()->json($translatedPosts);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {

        $image_url = Images::SavePostImages($request);

        if ($request->language == 'ar') {
            $post = Post::query()->create([
                'text' => [
                    'ar' => $request->text
                ],
                'market_id' => Auth::guard('market-api')->id()
            ]);

        } else {
            $post = Post::query()->create([
                'text' => [
                    'en' => $request->text
                ],
                'market_id' => Auth::guard('market-api')->id()

            ]);
        }

        foreach ($image_url as $image) {
            $photo = new Photo();
            $photo->url = $image;
            $post->photo()->save($photo);
        }

        $userName = Auth::guard('market-api')->user()->name;
        $title = 'new post';
        $message = $userName . ' added new post';
        $ids [] = FollowUpRequest::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'accepted')
            ->pluck('user_id');
        Notifications::notify($ids[0], $title, $message, 'user');


        return response()->json("post created successfully", Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        $photo = Photo::query()->where('photoable_id', '=', $post->id)
            ->where('photoable_type', '=', 'App\Models\Post')->get();

        $result['post'] = $post;
        $result['images'] = $photo;
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePostRequest $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->market_id != Auth::guard('market-api')->id()) {
            return response()->json('UNAUTHORIZED', Response::HTTP_UNAUTHORIZED);
        }

        if ($request->has('photo')) {
            $photos = Photo::query()->where('photoable_id', '=', $post->id)->get();

            Images::deletePhoto($photos);
            foreach ($photos as $item) {
                $item->delete();
            }

            //storing new photos
            $image_url = Images::SavePostImages($request);

            foreach ($image_url as $image) {
                $photo = new Photo();
                $photo->url = $image;
                $post->photo()->save($photo);
            }

        }

        if ($request->language == 'ar') {
            $post->update([
                'text' => [
                    'ar' => $request->text
                ],
            ]);

        } else {
            $post->update([
                'text' => [
                    'en' => $request->text
                ],
            ]);
        }

        $photo = Photo::query()->where('photoable_id', '=', $post->id)->get();


        $result['post'] = $post;
        $result['images'] = $photo;
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post): \Illuminate\Http\JsonResponse
    {
        if ($post->market_id != Auth::guard('market-api')->id()) {
            return response()->json('UNAUTHORIZED', Response::HTTP_UNAUTHORIZED);
        }

        $photo = Photo::query()->where('photoable_id', '=', $post->id)->get();
        Images::deletePhoto($photo);
        foreach ($photo as $item) {
            $item->delete();
        }
        $post->delete();


        return response()->json('post deleted successfully', Response::HTTP_OK);
    }


    public function ListMyPosts()
    {

        $post = Post::query()->with('photo')
            ->where('market_id', '=', Auth::guard('market-api')->id())
            ->get();

        return response()->json($post, Response::HTTP_OK);
    }

    public function ListMostLikedPosts()
    {

        $posts = Post::query()->with('photo')
            ->orderBy('numberOfLikes', 'desc')->take(10)
            ->get();
        foreach ($posts as $post) {
            $isLiked = Like::query()->where('user_id', '=', Auth::guard('api')->id())
                ->where('post_id', '=', $post->id)->value('isLike');
            if ($isLiked == null || $isLiked == false) {
                $post['isLiked'] = false;
            } else {
                $post['isLiked'] = true;
            }

            $post['market'] = Market::query()->find($post->market_id);
        }
        return response()->json($posts, Response::HTTP_OK);
    }

}
