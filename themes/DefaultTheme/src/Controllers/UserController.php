<?php

namespace Themes\DefaultTheme\src\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function profile()
    {
        $user        = auth()->user();
        $last_orders = $user->orders()->latest()->take(5)->get();

        return view('front::user.profile', compact('user', 'last_orders'));
    }

    public function update_profile(Request $request)
    {
        $this->validate($request, [
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'mobile'       => 'required|string|regex:/(09)[0-9]{9}/|digits:11|unique:users,username,' . auth()->user()->id,
            'email'        => 'string|email|max:191|unique:users,email,' . auth()->user()->id . '|nullable',
            'province_id'  => 'required|exists:provinces,id',
            'city_id'      => 'required|exists:cities,id',
            'postal_code' => 'required|numeric|digits:10',
            'address'      => 'required|string|max:300',
        ]);

        $address = auth()->user()->address;

        if (!$address) {
            $address = auth()->user()->address()->create([
                'province_id' => $request->province_id,
                'city_id'     => $request->city_id,
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
            ]);
        } else {
            $address->update([
                'province_id' => $request->province_id,
                'city_id'     => $request->city_id,
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
            ]);
        }

        auth()->user()->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'username'   => $request->mobile,
            'email'      => $request->email,
        ]);

        return response('success');
    }

    public function changePassword()
    {
        return view('front::auth.passwords.reset');
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'prev_password' => 'required'
        ]);

        if (!Hash::check($request->prev_password, auth()->user()->password)) {
            throw ValidationException::withMessages(['prev_password' => 'رمز عبور قبلی وارد شده اشتباه است']);
        }

        $this->validate($request, [
            'password' => 'required|min:8|confirmed'
        ]);

        $password = Hash::make($request->password);

        auth()->user()->update([
            'password'       => $password,
            'remember_token' => Str::random(60),
        ]);

        DB::table('sessions')->where('user_id', auth()->user()->id)->delete();

        return response('success');
    }

    public function forceChangePassword()
    {
        return view('front::auth.passwords.force-change');
    }

    public function forceUpdatePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:8|confirmed'
        ]);

        auth()->user()->update([
            'password'                 => Hash::make($request->password),
            'remember_token'           => Str::random(60),
            'force_to_password_change' => false,
        ]);

        DB::table('sessions')->where('user_id', auth()->user()->id)->delete();
        Auth::loginUsingId(auth()->user()->id);

        return response('success');
    }

    public function editProfile()
    {
        $user      = auth()->user();
        $provinces = Province::all();

        return view('front::user.edit-profile', compact('user', 'provinces'));
    }

    public function comments()
    {
        $comments = auth()->user()->comments()->latest()->get();

        return view('front::user.comments', compact('comments'));
    }
}
