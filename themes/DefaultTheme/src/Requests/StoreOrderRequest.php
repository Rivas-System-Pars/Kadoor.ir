<?php

namespace Themes\DefaultTheme\src\Requests;

use App\Models\Gateway;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $gateways = Gateway::active()->pluck('key')->toArray();
        $cart     = get_cart();

        $rules = [
            'name'        => 'required|string',
            'mobile'      => 'required|string|regex:/(09)[0-9]{9}/|digits:11',
			'province_id' => 'required|exists:provinces,id',
			'city_id'     => 'required|exists:cities,id',
			'address'     => 'required|string|max:300',
			'national_code'     => 'required|digits:10',
			'postal_code'     => 'required|digits:10',
			'telephone'     => 'nullable|regex:/^0[0-9]{2,}[0-9]{7,}$/',
			
//            'gateway'     => 'required|in:wallet,' . implode(',', $gateways),
//            'description' => 'nullable|string|max:1000',
        ];

//        if ($cart && $cart->hasPhysicalProduct()) {
//            $rules = array_merge($rules, [
//                'province_id' => 'required|exists:provinces,id',
//                'city_id'     => 'required|exists:cities,id',
//                'postal_code' => 'required|numeric|digits:10',
//                'address'     => 'required|string|max:300',
//                'carrier_id'  => 'required|exists:carriers,id'
//            ]);
//        }

        return $rules;
    }
	
	
	public function attributes(){
		return [
			'national_code'=>"کد ملی",
		];
	}
}
