<?php
namespace App\Http\Controllers;


use App\Category;
use App\Post;
use App\Tag;
use Session;
use DB;
use Auth;

use Illuminate\Http\Request;

class PostsController extends Controller
{
    // *
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response

    public function index()
    {

     return view('admin.posts.index')->with('posts', Post::all());
 }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories= Category::all();

        if($categories->count()==0)
        {
            Session::flash('info', 'You must have some categories before creating some posts');
            return redirect()->back();
        }
        return view('admin.posts.create')->with('categories',$categories)->with('tags',Tag::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        $this->validate($request, [

            'title'=>'required||max:255',
            'featured'=>'required||size:1200000',
            'content'=>'required',
            'category_id'=>'required',
            

        ]);

        $featured=$request->featured;
        $featured_new_name=time().$featured->getClientOriginalName();
        $featured->move('uploads/posts',$featured_new_name);

        $post=Post::create([
            'title'=>$request->title,
            'content'=>$request->content,
            'featured'=>'/uploads/posts/'.$featured_new_name,
            'category_id'=>$request->category_id,
            'slug'=>str_slug($request->title)
            'user_id'=>Auth::id;
        ]);        
        //many to many
        $post->tags()->attach($request->tags);

        Session::flash('success', 'Post created Succesfully !');

        return redirect()->route('posts');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $tagsNotInPost = DB::table("tags")->select('*')
        ->whereNotIn('id',function($query){
         $query->select('tag_id')->from('post_tag');
     })
        ->get();

        


        // $tag_id=Tag::select('id')->whereNotIn('id',$post_tag_id)->get();
        // echo $tag_id;
        $post= Post::find($id);
        return view('admin.posts.edit')->with('posts',$post)->with('categories',Category::all())->with('tags',Tag::all())->with('tagsNotInPost',$tagsNotInPost);
    }

    public function deletePostTag($id)
    {
        DB::table('post_tag')->where('tag_id', '=', $id)->delete();
        
        return redirect()->back();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required'
        ]);

        if($request->hasFile('featured'))
        {
            $featured = $request->featured;
            $featured_new_name = time() . $featured->getClientOriginalName();
            $featured->move('uploads/posts', $featured_new_name);
            $post->featured ='uploads/posts/'.$featured_new_name;
            
        }
        $post->title = $request->title;
        $post->content = $request->content;
        $post->category_id = $request->category_id;
        $post->save();

        $post->tags()->attach($request->tags);


        Session::flash('success','Post has been updated successfully !');
           
        return redirect()->back();


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post= Post::find($id);

        $post->delete();

        Session::flash('success','The Post has been deleted Succesfully !');

        return redirect()->route('posts');
    }
    public function trashed(){




        $posts= Post::onlyTrashed()->get();

        return view('admin.posts.trash')->with('posts',$posts);
    } 

    public function kill($id){

        $post=Post::withTrashed()->where('id',$id)->first();

        $post->forceDelete();

        Session::flash('success','Post Deleted Permenantly !');

        return redirect()->route('post.trashed');




    }

    public function restore($id)
    {
        $post=Post::withTrashed()->where('id',$id)->first();

        $post->restore();

        Session::flash('success','Post Restored Succesfully !');

        return redirect()->route('post.trashed');
    }
}
