<?php

namespace App\Http\Requests\Auth;

use App\Rules\NotSpecialChar;
use App\Rules\CheckeNationalCode;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'regex:/(09)[0-9]{9}/', 'digits:11', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed:confirmed'],
			'notional_code'  => ['required', new CheckeNationalCode()],
			'city_id'  => ['required', 'exists:cities,id'],
			'zip_code'  => ['required', 'regex:/(?!(\d)\1{3})[13-9]{4}[1346-9][013-9]{5}/'],
			'address'  => ['required', 'string'],
            'captcha'    => ['required', 'captcha'],
        ];
    }

    public function attributes()
    {
        return [
            'notional_code' => 'کد ملی',
			'city_id'=>'شهر',
			'zip_code'=>'کد پستی'
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'لطفا یک شماره موبایل معتبر وارد کنید',
            'username.string'   => 'لطفا یک شماره موبایل معتبر وارد کنید',
            'username.regex'    => 'لطفا یک شماره موبایل معتبر وارد کنید',
            'username.digits'   => 'لطفا یک شماره موبایل معتبر وارد کنید',
            'username.unique'   => 'شماره موبایل وارد شده تکراری است',
			'first_name.regex'    => ':attribute باید به فارسی وارد شود.',
			'last_name.regex'    => ':attribute باید به فارسی وارد شود.',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'username'   => convertPersianToEnglish($this->input('username'))
        ]);
    }
}
