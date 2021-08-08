<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BlogCreateRequest;

class BlogController extends Controller
{
    public function index() {

        $user = Auth::user();

        if( $user->hasRole('admin') || $user->hasRole('manager')) {
            $blogs = Blog::all();
        } else {
            $blogs = Blog::where('created_by', $user->id )->get();
        }
        
        return response([ 
            'blogs' => BlogResource::collection($blogs),
            'message' => 'Successful'
        ], 200);
    }

    public function show(Blog $blog) {
    	return $blog;
    }

    public function store(BlogCreateRequest $request) {
        $created_by = Auth::user()->id;
        $request["created_by"] = $created_by;
    	$blog = Blog::create($request->all());
    	return response()->json($blog,201);
    }

    public function update(Request $request, Blog $blog) {
    	$blog->update($request->all());
    	return response()->json($blog,200);
    }

    public function delete(Request $request, Blog $blog) {
    	$blog -> delete();
    	return response()->json(null, 204);
    }
}
