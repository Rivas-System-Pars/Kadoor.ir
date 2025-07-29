<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;

class CheckeNationalCode implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkMeliCode($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'کد ملی نامعتبر است';
    }
	
	function checkMeliCode($meli)
	{

	  $cDigitLast = substr($meli , strlen($meli)-1);
	  $fMeli = strval(intval($meli));

	  if((str_split($fMeli))[0] == "0" && !(8 <= strlen($fMeli)  && strlen($fMeli) < 10)) return false;

	  $nineLeftDigits = substr($meli , 0 , strlen($meli) - 1);

	  $positionNumber = 10;
	  $result = 0;

	  foreach(str_split($nineLeftDigits) as $chr){
			$digit = intval($chr);
			$result += $digit * $positionNumber;
			$positionNumber--;
	  }

	  $remain = $result % 11;

	  $controllerNumber = $remain;

	  if(2 < $remain){
		$controllerNumber = 11-$remain;
	  }

	  return $cDigitLast == $controllerNumber;

	}
}
