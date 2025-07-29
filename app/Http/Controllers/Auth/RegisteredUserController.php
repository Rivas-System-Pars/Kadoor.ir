<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Province;
use App\Models\City;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $view = config('front.pages.register');

        if (!$view) {
            abort(404);
        }
		$provinces = Province::active()->orderBy('ordering')->get();

        return view($view,compact('provinces'));    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
				

        $data             = $request->validated();
        $data['password'] = Hash::make($data['password']);
		$data['mobile'] = $data['username'];
		$data['notional_code'] = $data['notional_code'];
		$data['city_id'] = $data['city_id'];
		$data['zip_code'] = $data['zip_code'];
		$data['address_txt'] = $data['address'];
        $user = User::create($data);
		$city = City::find($request->city_id);
		Http::withHeaders([
                'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde'
            ])->asForm()->post('https://webcomapi.ir/api/Store/RegisterUser2',[
				'phoneNumber'=>$request->username,
				'address'	=>	$request->address,
				'NationalCode'=>$request->national_code,
				'PostCode'	=>	$request->zip_code,
				'fullName'=>$request->first_name." ".$request->last_name,
				//'Tel'=>$request->mobile,
				'State'=>$city->province->name,
				'City'=>$city->name,
				//'Area'=>$request->address
			]);

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://webcomapi.ir/api/Store/RegisterUser',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => array('phoneNumber' => $user->username ,'fullName' => $user->firstname.' '.$user->lastname),
        //     CURLOPT_HTTPHEADER => array(
        //         'apiKey: 7a2a1be2*d422*4d70*8b61*affdde'
        //     ),
        // ));
        // $response = curl_exec($curl);
        // curl_close($curl);

        event(new Registered($user));

        Auth::login($user);

        return response('success');
    }
}
