<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Blog;
use App\Models\Center;
use App\Models\Group;

use App\Repositories\BlockUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Auth;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = array();
        $centers = array();
        $groups = array();
        $users = array();

        $currentUser = Auth::user();
        
        $users = User::with(['roles', 'centers', 'groups']);
        $users->where('is_super_admin', '=', 0)->where('is_active', '=', 1);
        $users->whereHas('roles', function ($query) {
            $query->where('name', '=', 'Member');
        });
        
        if ($currentUser->hasRole('Group Leader') || $currentUser->hasRole('Member')) {
            $group_ids = $currentUser->groups->pluck('id');
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.group_id', $group_ids)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            $users->whereHas('groups', function ($query) use ($group_ids) {
                $query->whereIn('group_id', $group_ids);
            });
        } else if ($currentUser->hasRole('Center Admin')) {
            $center_ids = $currentUser->centers->pluck('id');
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.center_id', $center_ids)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            $groups = Group::whereIn('center_id', $center_ids)->with('center')->get();
            $users->whereHas('centers', function ($query) use ($center_ids) {
                $query->whereIn('center_id', $center_ids)->where('is_active', '=', 1);
            });
        } else {
            // all users for super admin - no filter here
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            $groups = Group::all();
            $centers = Center::all()->where('is_active', '=', 1);
        }
        $users = $users->select([
            'photo_url', 'id', 'name',
        ])->get();

        $data = [
            'blogs' => $blogs,
            'centers' => $centers,
            'groups' => $groups,
            'users' => $users,
        ];
        return view('blog.index', $data);
    }

    public function createBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog-image' => 'required|image|mimes:gif,jpeg,webp,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        try {
            $blog_image = $request->file('blog-image');
            $data['blog_image'] = time().'.'.$blog_image->getClientOriginalExtension();
            $blog_image->move(public_path('/uploads/blogs'), $data['blog_image']);
            $data['description'] = $request->input('description');
            
            $currentUser = Auth::user();
            $data['center_id'] = $currentUser->centers->pluck('id')[0];
            $data['group_id'] = $currentUser->groups->pluck('id')[0];
            $data['user_id'] = $currentUser->id;

            $blog = Blog::create($data);
            if($blog) {
                $new_blog = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.id', '=', $blog->id)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
                
                $html = view('blog.blog', ['key' => 0, 'blog' => $new_blog[0]])->render();
                return response()->json([
                    'status' => true,
                    'html' => $html,
                    'message' => "You've created a new blog successfully.",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again later.'
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later.'
            ]);
        }
    }

    public function deleteBlog(Request $request)
    {
        $blog_id = $request->get('blog_id');
        $blog = Blog::where('id', $blog_id)->first();
        if($blog) {
            $can_delete = false;

            //check if the user has permission to delete the blog
            $currentUser = Auth::user();
            if($currentUser->hasRole('Member')) {
                if($blog->user_id == $currentUser->id) {
                    $can_delete = true;
                }
            } else if ($currentUser->hasRole('Center Admin')) {
                $centers = $currentUser->centers->pluck('id');
                if(in_array($blog->center_id, $centers->all())) {
                    $can_delete = true;
                }
            } else if ($currentUser->hasRole('Group Leader')) {
                $groups = $currentUser->groups->pluck('id');
                if(in_array($blog->group_id, $groups->all())) {
                    $can_delete = true;
                }
            } else {
                // all users for super admin - no filter here
            }
            if($can_delete) {
                $deleted_blog = Blog::find($blog_id)->delete();
                if($deleted_blog) {
                    return response()->json([
                        'status' => true,
                        'message' => "You've deleted the blog successfully."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Something went wrong. Please try again later."
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "You don't have permission to delete the blog."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "The blog doesn't exist."
            ]);
        }
    }

    public function filterBlogs(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        if($type == "center") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.center_id', '=', $id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } elseif($type == "group") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.group_id', '=', $id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } elseif($type == "user") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.user_id', '=', $id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } else {
            $currentUser = Auth::user();
            if ($currentUser->hasRole('Group Leader') || $currentUser->hasRole('Member')) {
                $group_ids = $currentUser->groups->pluck('id');
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.group_id', $group_ids)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            } else if ($currentUser->hasRole('Center Admin')) {
                $center_ids = $currentUser->centers->pluck('id');
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.center_id', $center_ids)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            } else {
                // all users for super admin - no filter here
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            }
        }
        $html = view('blog.blog-list', ['blogs' => $blogs])->render();
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => "You've filtered blogs successfully.",
        ]);
    }
    public function expandBlogs(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        $last_blog_id = $request->get('last_blog_id');
        if($type == "center") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.center_id', '=', $id)->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } elseif($type == "group") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.group_id', '=', $id)->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } elseif($type == "user") {
            $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.user_id', '=', $id)->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
        } else {
            $currentUser = Auth::user();
            if ($currentUser->hasRole('Group Leader') || $currentUser->hasRole('Member')) {
                $group_ids = $currentUser->groups->pluck('id');
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.group_id', $group_ids)->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            } else if ($currentUser->hasRole('Center Admin')) {
                $center_ids = $currentUser->centers->pluck('id');
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->whereIn('blogs.center_id', $center_ids)->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            } else {
                $blogs = Blog::join('users', 'blogs.user_id', '=', 'users.id')->where('blogs.id', '<', $last_blog_id)->orderby('blogs.created_at', 'desc')->limit(5)->get(['blogs.*', 'users.name', 'users.photo_url', 'users.gender']);
            }
        }
        $html = "";
        foreach ($blogs as $key => $blog) {
            $html .= view('blog.blog', ['key' => $key, 'blog' => $blog])->render();
        }
        
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => "You've loaded more blogs successfully.",
        ]);
    }
}
