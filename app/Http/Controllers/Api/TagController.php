<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Resources\TagCollection;

class TagController extends Controller
{
    public function index()
    {
        return new TagCollection(Tag::paginate());
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }
}
