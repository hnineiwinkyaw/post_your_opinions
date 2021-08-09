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
     *      tags={"Blogs"},
     *      operationId="get Blogs ",
     *      summary="Get list of blogs",
     *      security={ {"bearer": {} }},
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
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="integer",
     *                                      description="User Id of the blog created"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated timestamp"
     *                                  )
     *                          )
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message"
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

    /**
     * @OA\Get(
     *      path="/blogs/{id}",
     *      operationId="get Blog Details ",
     *      tags={"Blogs"},
     *      summary="Get detail of blog object",
     *      security={ {"bearer": {} }},
     *      description="Returns blog detail object",
     *      @OA\Parameter(
     *          name="id",
     *          description="blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="integer",
     *                                      description="User Id of the blog created"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated timestamp"
     *                                  )
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
    public function show(Blog $blog) {

        if( $this->isBlogCrudAllow($blog->created_by)) {
            return response()->json($blog,200);
        } else {
            return response()->json($this->resourceForbiddenMessage(),403);
        }
    }

    /**
     * @OA\Post(
     *      path="/blogs",
     *      operationId="create blog",
     *      tags={"Blogs"},
     *      security={ {"bearer": {} }},
     *      summary="Create new blog",
     *      description="Create new blog",
     *      @OA\RequestBody(
     *          required=true,
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  )
     *                 )
     *             )
     *         }
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="integer",
     *                                      description="User Id of the blog created"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated timestamp"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
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
    public function create(BlogCreateRequest $request) {
        $created_by = Auth::user()->id;
        $request["created_by"] = $created_by;
    	$blog = Blog::create($request->all());
    	return response()->json($blog,201);
    }

    /**
     * @OA\Put(
     *      path="/blogs/{id}",
     *      operationId="update Blog Details ",
     *      tags={"Blogs"},
     *      summary="update blog object",
     *      security={ {"bearer": {} }},
     *      description="Update blog detail object",
     *      @OA\Parameter(
     *          name="id",
     *          description="blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="integer",
     *                                      description="User Id of the blog created"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated timestamp"
     *                                  )
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
    public function update(Request $request, Blog $blog) {

        if( $this->isBlogCrudAllow($blog->created_by)) {
            $blog->update($request->all());
            return response()->json($blog,200);
        } else {
            return response()->json($this->resourceForbiddenMessage(),403);
        }	
    }

    /**
     * @OA\Delete(
     *      path="/blogs/{id}",
     *      operationId="Delete Blog ",
     *      tags={"Blogs"},
     *      summary="Delete blog by id",
     *      security={ {"bearer": {} }},
     *      description="Delete blog by id",
     *      @OA\Parameter(
     *          name="id",
     *          description="blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="content",
     *                                      type="string",
     *                                      description="Blog Content"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="integer",
     *                                      description="User Id of the blog created"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated timestamp"
     *                                  )
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
