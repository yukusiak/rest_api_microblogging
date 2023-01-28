<?php

// namespace App\Http\Controllers;
// use App\Models\Post;

// use Illuminate\Http\Request;

// class PostController extends Controller
// {
//     public function index(){
//         $post = Post::find(1);
//         $posts = Post::all();
//         $posts = Post::where('is_published',1)->get();
//         dump($posts);
//     }

//     public function create(){
//         $postsArr = [
//             'title' => 'title',
//             'content' => 'content',
//             'image' => 'image',
//             'likes' => 80,
//             'is_published' => 1,
//         ];

//         Post::create($postsArr);
//     }

//     public function update(){
//         $post = Post::find(3);
//         $postsArr = [
//             'title' => 'title',
//             'content' => 'content',
//             'image' => 'image',
//             'likes' => 80,
//             'is_published' => 1,
//         ];
//         $post->update($postsArr);
//     }
// }
