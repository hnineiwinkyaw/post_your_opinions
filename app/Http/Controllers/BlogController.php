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
    /**
     * @OA\Get(
     *      path="/blogs",
     *      operationId="get Blogs ",
     *      summary="Get list of blogs",
     *      description="Returns list of blogs",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="blogs",
     *                         type="array",
     *                         collectionFormat="multi",
     *                         @OA\Items(
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  )
     *                              )
     *                          )
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="The response data",
     *                         @OA\Items
     *                     )
     *                 )
     *             )
     *         }
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

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

        if( $this->isBlogCrudAllow($blog->created_by)) {
            return response()->json($blog,200);
        } else {
            return response()->json($this->resourceForbiddenMessage(),403);
        }
    }

    public function create(BlogCreateRequest $request) {
        $created_by = Auth::user()->id;
        $request["created_by"] = $created_by;
    	$blog = Blog::create($request->all());
    	return response()->json($blog,201);
    }

    public function update(Request $request, Blog $blog) {

        if( $this->isBlogCrudAllow($blog->created_by)) {
            $blog->update($request->all());
            return response()->json($blog,200);
        } else {
            return response()->json($this->resourceForbiddenMessage(),403);
        }	
    }

    public function destroy(Request $request, Blog $blog) {

        if( $this->isBlogCrudAllow($blog->created_by)) {
            $blog -> delete();
            return response()->json(null, 204);
        } else {
            return response()->json($this->resourceForbiddenMessage(),403);
        }
    }

    private function isBlogCrudAllow($created_by) {
        $user = Auth::user();
        return $user->hasRole('admin') || $user->hasRole('manager') || $created_by == Auth::user()->id;
    }

    private function resourceForbiddenMessage() {
        return ["error"=>"Resource Forbidden", "status"=> 403];
    }
}
