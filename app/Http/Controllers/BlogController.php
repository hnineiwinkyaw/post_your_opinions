<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
    public function index() {
    	$blogs = Blog::all();
        return response([ 
            'blogs' => BlogResource::collection($blogs),
            'message' => 'Successful'
        ], 200);
    }

    public function show(Blog $blog) {
    	return $blog;
    }

    public function store(Request $request) {
    	$blog = Blog::create($request->all());
    	return response()->json($blog,201);
    }

    public function update(Request $request, Blog $blog) {
    	// $blog->update($request->all());
    	// return response()->json($blog,200);
        $response = ['message' => 'update function'];
        return response($response, 200);
    }

    public function delete(Request $request, Blog $blog) {
    	$blog -> delete();
    	return response()->json(null, 204);
    }
}
