<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Validator;
use Exception;
use App\Follows;
use App\Likes;
use App\Comments;
use App\ImagesPost;

class UserController extends Controller
{
    public function login(Request $request) { 
        $validator = Validator::make($request->all(), 
            [
                'username' => 'alpha_dash|max:255',
                'email' => 'required|max:255',
                'password' => 'required',
            ],
            [
                'required' => ':attribute cannot be empty.',
                'alpha_dash' => ':attribute cannot contain spaces.',
            ], 
            [
                'username' => 'Username',
                'email' => 'Email address',
                'password' => 'Password',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        if(User::where('email', $request->email)->exists()){
            $user = User::where('email', $request->email)->where('deleted_at', NULL)->first();
            if($user->exists()){
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $token = Auth::user()->createToken('login')->accessToken;
                    
                    if($user->image_profile != null) {
                        $imagePath = 'public/images/users/'.$user->id.'/image_profile'.'/';
                        if(Storage::disk('local')->exists($imagePath.$user->image_profile)) {
                            $img = base64_encode(Storage::disk('local')->get($imagePath.$user->image_profile));
                            $image_profile = 'data:image/'.pathinfo($user->image_profile)['extension'].';base64,'.$img;
                        } else {
                            $image_profile = '';
                        }
                    } else {
                        $image_profile = '';
                    }
                    return response()->json([
                        'message' => 'Successful!',
                        'user' => collect($user)->merge(['image_profile' => $image_profile]),
                        'access_token' => $token
                    ], 200);
                } else {
                    return response()->json(['message' => 'Incorrect password.'], 404);
                }
            } else {
                return response()->json(['message' => 'Account has deleted.'], 404);
            }
        } else {
            return response()->json(['message' => 'Email doesn\'t exists.'], 404);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), 
            [
                'username' => 'required|alpha_dash|unique:users|max:255',
                'email' => 'required|unique:users|max:255',
                'password' => 'required',
            ],
            [
                'required' => ':attribute cannot be empty.',
                'alpha_dash' => ':attribute cannot contain spaces, dots, special characters.',
                'unique' => ':attribute has exists.',
            ], 
            [
                'username' => 'Username',
                'email' => 'Email address',
                'password' => 'Password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        $user = new User;
        $user->username = trim($request->username);
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Successful'], 200);
    }

    public function logout(Request $request) {
        if(Auth::user()) {
            $token = $request->user()->token();
            $token->revoke();
            return response()->json(['message' => 'Logout successful.'], 200);
        } else {
            return response()->json(['message' => 'Not logged in.'], 200);
        }
    }

    public function index(Request $request) {
        $users = User::inRandomOrder()->limit(25)->get();
        return response()->json($users, 200);
    }

    public function profile(Request $request, $username) {
        $user = User::whereNull('deleted_at')->where('username', $username)->first();
        if($user != null) {
            if($user->image_main != null) {
                $imagePath = 'public/images/users/'.$user->id.'/image_main'.'/';
                if(Storage::disk('local')->exists($imagePath.$user->image_main)) {
                    $img = base64_encode(Storage::disk('local')->get($imagePath.$user->image_main));
                    $image_main = 'data:image/'.pathinfo($user->image_main)['extension'].';base64,'.$img;
                } else {
                    $image_main = null;
                }
            } else {
                $image_main = null;
            }

            if($user->image_profile != null) {
                $imagePath = 'public/images/users/'.$user->id.'/image_profile'.'/';
                if(Storage::disk('local')->exists($imagePath.$user->image_profile)) {
                    $img = base64_encode(Storage::disk('local')->get($imagePath.$user->image_profile));
                    $image_profile = 'data:image/'.pathinfo($user->image_profile)['extension'].';base64,'.$img;
                } else {
                    $image_profile = null;
                }
            } else {
                $image_profile = null;
            }

            $arrPosts = [];
            foreach($user->posts as $post) {
                $likes = Likes::whereNull('deleted_at')->where('post_id', $post->id)->pluck('user_id');
                $comments = Comments::whereNull('deleted_at')->where('post_id', $post->id)->select('user_id', 'comment', 'created_at')->get();
                $image = ImagesPost::where('post_id', $post->id)->first();
                if($image) {
                    $image = $image->path;
                    $imagePath = 'public/images/posts/'.$user->id.'/';
                    if(Storage::disk('local')->exists($imagePath.$image)) {
                        $img = base64_encode(Storage::disk('local')->get($imagePath.$image));
                        if(pathinfo($image)['extension'] == "pdf") {
                            $path = 'data:application/pdf;base64,'.$img;
                        } else {
                            $path = 'data:image/'.pathinfo($image)['extension'].';base64,'.$img;
                        }
                    } else {
                        $path = "";
                    }
                } else {
                    $path = "";
                }
                $arrPosts[] = collect($post)->merge(['path' => $path, 'likes' => $likes, 'comments' => $comments]);
            }

            $followers = Follows::whereNull('deleted_at')->where('user_id_follow', $user->id)->pluck('user_id');
            $users_followers = User::whereNull('deleted_at')->whereIn('id', $followers)->get();
            $arrUsersFoffowers = [];
            foreach($users_followers as $user_follow) {
                if($user_follow->image_profile != null) {
                    $imagePath = 'public/images/users/'.$user_follow->id.'/image_profile'.'/';
                    if(Storage::disk('local')->exists($imagePath.$user_follow->image_profile)) {
                        $img = base64_encode(Storage::disk('local')->get($imagePath.$user_follow->image_profile));
                        $imageProfile = 'data:image/'.pathinfo($user_follow->image_profile)['extension'].';base64,'.$img;
                    } else {
                        $imageProfile = '';
                    }
                } else {
                    $imageProfile = '';
                }
                $arrUsersFoffowers[] = collect($user_follow)->merge(['image_profile' => $imageProfile]);                
            }
            
            $following = Follows::whereNull('deleted_at')->where('user_id', $user->id)->pluck('user_id_follow');
            $users_following = User::whereNull('deleted_at')->whereIn('id', $following)->get();
            $arrUsersFoffowing = [];
            foreach($users_following as $user_following) {
                if($user_following->image_profile != null) {
                    $imagePath = 'public/images/users/'.$user_following->id.'/image_profile'.'/';
                    if(Storage::disk('local')->exists($imagePath.$user_following->image_profile)) {
                        $img = base64_encode(Storage::disk('local')->get($imagePath.$user_following->image_profile));
                        $imageProfile = 'data:image/'.pathinfo($user_following->image_profile)['extension'].';base64,'.$img;
                    } else {
                        $imageProfile = '';
                    }
                } else {
                    $imageProfile = '';
                }
                $arrUsersFoffowing[] = collect($user_following)->merge(['image_profile' => $imageProfile]);                
            }

            return response()->json([
                'user' => collect($user)->merge(['image_main' => $image_main, 'image_profile' => $image_profile]),
                'posts' => $arrPosts,
                'followers' => $arrUsersFoffowers,
                'following' => $arrUsersFoffowing,
            ], 200);
        }
        return response()->json(['message' => 'Not Found.'], 404);
    }
    public function profileUpdate(Request $request, $id) {
        $request = $request->data;
        $validator = Validator::make($request, 
            [
                'username' => 'required|alpha_dash|unique:users,username,'.$id.',id,deleted_at,NULL|max:100',
                'email' => 'required|unique:users,email,'.$id.',id,deleted_at,NULL|max:100',
            ],
            [
                'required' => ':attribute cannot be empty.',
                'alpha_dash' => ':attribute cannot contain spaces, dots, special characters.',
                'unique' => ':attribute has exists.',
            ],
            [
                'username' => 'Username',
                'email' => 'Email address',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        $user = User::find($id);
        $user->name = $request['name'];
        $user->username = trim($request['username']);
        $user->email = $request['email'];
        $user->address = $request['address'];
        $user->birth_date = $request['birth_date'];
        $user->description = $request['description'];
        $user->gender = $request['gender'];
        $user->phone_number = $request['phone_number'];
        
        $image_main = $request['image_main'];
        if (preg_match('/^data:image\/(\w+);base64,/', $image_main)) {
            //delete
            $imagePath = 'public/images/users/'.$id.'/image_main'.'/';
            if($user->image_main != null) {
                if(Storage::disk('local')->exists($imagePath.$user->image_main)) {
                    Storage::disk('local')->delete($imagePath.$user->image_main);
                }
            }

            $extension = explode('/', mime_content_type($image_main))[1];
            $imageName = time().'.'. $extension;
            $img = Image::make(base64_decode(preg_replace('/^data:image\/(\w+);base64,/', '',$image_main)));
            if ($img->width() >= $img->height() && $img->width() > 2048) {
                $img->resize(2048, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else if($img->height() > $img->width() && $img->height() > 2048){
                $img->resize(null, 2048, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } //resize 2048

            Storage::disk('local')->put($imagePath.$imageName, $img->stream());
            $user->image_main = $imageName;
        }

        $image_profile = $request['image_profile'];
        if (preg_match('/^data:image\/(\w+);base64,/', $image_profile)) {
            //delete
            $imagePath = 'public/images/users/'.$id.'/image_profile'.'/';
            if($user->image_main != null) {
                if(Storage::disk('local')->exists($imagePath.$user->image_profile)) {
                    Storage::disk('local')->delete($imagePath.$user->image_profile);
                }
            }

            $extension = explode('/', mime_content_type($image_profile))[1];
            $imageName = time().'.'. $extension;
            $img = Image::make(base64_decode(preg_replace('/^data:image\/(\w+);base64,/', '',$image_profile)));
            if ($img->width() >= $img->height() && $img->width() > 2048) {
                $img->resize(2048, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else if($img->height() > $img->width() && $img->height() > 2048){
                $img->resize(null, 2048, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } //resize 2048

            Storage::disk('local')->put($imagePath.$imageName, $img->stream());
            $user->image_profile = $imageName;
        }
        
        $user->save();
        return response()->json($user, 200);
    }

    public function follow(Request $request) {
        if(isset($request->user_id) && isset($request->id)) {
            $follow = new Follows;
            $follow->user_id = $request->user_id;
            $follow->user_id_follow = $request->id;
            $follow->save();
            return response()->json(['success' => true], 200);   
        } else {
            return response()->json(['failed' => true], 404);
        }
    }
    public function unfollow(Request $request) {
        if(isset($request->user_id) && isset($request->id)) {
            $unfollow = Follows::where('user_id', $request->user_id)->where('user_id_follow', $request->id)->forceDelete();
            return response()->json(['success' => true], 200);   
        } else {
            return response()->json(['failed' => true], 404);
        }
    }
}
        