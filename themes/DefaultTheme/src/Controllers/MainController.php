<?php

namespace Themes\DefaultTheme\src\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Gateway;
use App\Models\Province;
use App\Models\Widget;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Http\Controllers\Back\ProductController;
use Storage;
use App\Jobs\ResizeProductImage2;

class MainController extends Controller
{
    public function index()
    {
		$widgets = Widget::with('options')
            ->where('theme', current_theme_name())
            ->where('is_active', true)
            ->orderBy('ordering')
            ->get();
        return view('front::index', compact('widgets'));
    }

    public function checkout()
    {
        $cart     = auth()->user()->cart;
        $gateways = Gateway::active()->orderBy('ordering')->get();

        if (!$cart || !$cart->products->count() || !check_cart_quantity()) {
            return redirect()->route('front.cart');
        }

        $discount_status = check_cart_discount();

        $provinces       = Province::active()->orderBy('ordering')->get();
        $wallet          = auth()->user()->getWallet();
        $city_id         = auth()->user()->address  ? auth()->user()->address->city_id : null;
        $carriers        = Carrier::active()->latest()->get();

        return view('front::checkout', compact(
            'provinces',
            'discount_status',
            'gateways',
            'wallet',
            'city_id',
            'carriers'
        ));
    }

    public function getPrices(Request $request)
    {
        $cart     = auth()->user()->cart;

        if ($request->city_id) {
            $request->validate([
                'city_id' => 'required|exists:cities,id',
            ]);
        }

        if ($request->carrier_id) {
            $request->validate([
                'carrier_id' => 'required|exists:carriers,id',
            ]);
        }

        $carriers = Carrier::active()->latest()->get();

        return [
            'checkout_sidebar'   => view('front::partials.checkout-sidebar', [
                'city_id'        => $request->city_id,
                'carrier_id'     => $request->carrier_id
            ])->render(),

            'carriers_container' => view('front::partials.carriers-container', [
                'city_id'        => $request->city_id,
                'cart'           => $cart,
                'carrier_id'     => $request->carrier_id,
                'carriers'       => $carriers
            ])->render(),
        ];
    }

    public function captcha()
    {
        return response(['captcha' => captcha_src('flat')]);
    }
}
