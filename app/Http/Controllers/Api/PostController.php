<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Filters\SimpleFilter;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\BulkStorePostRequest;
use Illuminate\Support\Arr;

use App\Http\Resources\RatingCollection;


class PostController extends Controller
{
    public function index(Request $request)
    {
        $filter = new SimpleFilter();
        $queryItems = $filter->transform($request);

        $includeUser = $request->query('includeUser');
        $includeTags = $request->query('includeTags');

        $posts = Post::where($queryItems);

        if ($includeUser) {
            $posts = $posts->with('user');
        }

        if ($includeTags) {
            $posts = $posts->with('tags');
        }

        return new PostCollection($posts->paginate()->appends($request->query()));
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->all());
        $post->tags()->sync((array)$request->all()['tags']);
        return new PostResource($post);
    }

    public function bulkStore(BulkStorePostRequest $request)
    {
        $bulk = collect($request->all())->map(function ($arr, $key) {
            return Arr::except($arr, ['publishDate', 'isPublished', 'tags']);
        });
        Post::insert($bulk->toArray());
    }

    public function update(Request $request, Post $post)
    {
        $post->update($request->all());
        $post->tags()->sync((array)$request->all()['tags']);
    }

    /**
     * Fiter by userId.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function findByUserId(Request $request, $id)
    {
        $posts = Post::where('user_id', '=', $id)->with('user')->with('tags');
        return new PostCollection($posts->paginate()->appends($request->query()));
    }

    /**
     * Fiter by tagIds.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ids
     * @return \Illuminate\Http\Response
     */
    public function findByTagIds(Request $request, Post $post, $ids)
    {
        return new PostCollection($post->findByTagIds($ids)->paginate()->appends($request->query()));
    }

    /**
     * Fiter by tagNames.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $names
     * @return \Illuminate\Http\Response
     */
    public function findByTagNames(Request $request, Post $post, $names)
    {
        return new PostCollection($post->findByTagNames($names)->paginate()->appends($request->query()));
    }

    /**
     * Build user activity rating.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $period
     * @return \Illuminate\Http\Response
     */
    public function findByRating(Post $post, $period)
    {
        return new RatingCollection($post->findByRating($period)->get());
    }
}
