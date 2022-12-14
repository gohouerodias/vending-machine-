<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.home');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function profileUpdate(Request $request)
    {
        $id = Auth::id();
        $user = User::find($id);

        $user->name = request('name', null);
        $user->email = request('email', null);
        $user->address = request('address', null);
        $user->country = request('country') ? request('country') : $user->country;

        $user->phoneNumber = request('phoneNumber') ? request('phoneNumber') : $user->phoneNumber;
        $user->email_verified_at = null;
        $user->save();

        return redirect()->back()->with('status');
    }


    public function changepassword(Request $request)
    {
        $id = Auth::id();
        $user = User::find($id);
        $request->validate([
            'password' => 'min:6|string|max:255',
            'new_password' => 'min:6|string|max:255',

        ]);

        if (password_verify(request('password'), $user->password)) {
            $user->password = password_hash(request('new_password'), null);
            $user->email_verified_at = null;
            $user->save();
            return redirect()->back()->with('status');
        } else {
            return redirect()->back()->withErrors(
                [
                    'password' => 'Erreur retry!'
                ]
            );
        }
    }

    public function imageUploadPost(Request $request)
    {
        $id = Auth::id();
        $user = User::find($id);
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = 'profile.' . $request->image->extension();
        /* Store $imageName name in DATABASE from HERE */
        $user->image = $imageName;
        $user->save();
        $request->image->move(public_path('images'), $imageName);
        return back()
            ->with('success', 'You have successfully upload image.');
    }
    public function updateP()
    { // Removed the type-hinted User model instance

        $userprofile = Auth::user(); // Used this very authenticated user object to update the new values below

        $this->validate(request(), [
            'username' => 'required|string|regex:/\w*/|max:255|unique:users,username,' . $userprofile->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userprofile->id,
            'password' => 'required|string|min:8|confirmed',
            'about' => 'nullable|regex:/\w*/|string|min:8',
            'user_avatar' => 'required|file|image|max:500'
        ]);

        // File upload
        /*  if (request()->hasFile('user_avatar')){

            $path1 = request()->file('user_avatar')->store('Userprofile/upload1', 'public');
            $path2 = request()->file('user_avatar')->store('Userprofile/upload2', 'public');

            $profileImage1 = Image::make(public_path('storage/' . $path1))->fit(40,40);
            $profileImage1->save();
            $profileImage2 = Image::make(public_path('storage/' . $path2))->fit(60,60);
            $profileImage2->save();
        }
        else{
            $path1 = $userprofile->user_avatar;
            $path2 = $userprofile->user_photo_60by60;
        }**/


        // This "$userprofile" variable is the authenticated user object I mentioned at the top which I am using here to update new values

        /* $userprofile->update([
            'username' => strip_tags(request('username')),
            'name' => strip_tags(request('name')),
            'email' => strip_tags(request('email')),
            'user_avatar' => strip_tags($path1),
            'user_photo_60by60' => strip_tags($path2),
            'password' => Hash::make(request('password')),
            'about' => strip_tags(request('about')),
            ]);


        session()->flash(
            'feedback', 'Your profile was successfully updated.'
        );

        // New values were received and updated and feedback was shown...
        return redirect()->back()->with('status','Products Updated Successfully');
        //return redirect()->route('profile.settings');*/
    }
}
