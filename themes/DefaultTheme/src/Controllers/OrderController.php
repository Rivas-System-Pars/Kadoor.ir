<?php

namespace Themes\DefaultTheme\src\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Jobs\CancelOrder;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\City;
use App\Models\Province;
use App\Models\Transaction;
use App\Models\WalletHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Multipay\Invoice;
use Themes\DefaultTheme\src\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('front::user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id != auth()->user()->id) {
            abort(404);
        }

        $gateways = Gateway::active()->get();
        $wallet   = auth()->user()->getWallet();

        return view('front::user.orders.show', compact(
            'order',
            'gateways',
            'wallet'
        ));
    }

    public function store(StoreOrderRequest $request)
    {
        $user = auth()->user();

        $cart = $user->cart;

        if (!$cart || !$cart->products->count() || !check_cart_quantity()) {
            return redirect()->route('front.cart');
        }

        if (!check_cart_discount()['status']) {
            return redirect()->route('front.checkout');
        }

//        $gateway  = Gateway::where('key', $request->gateway)->first();
        $data     = $request->validated();

        $data['shipping_cost']      = $cart->shippingCostAmount($request->city_id, $request->carrier_id);
        $data['price']              = $cart->finalPrice($request->city_id, $request->carrier_id);
        $data['status']             = 'unpaid';
        $data['discount_amount']    = $cart->totalDiscount();
        $data['discount_id']        = $cart->discount_id;
        $data['user_id']            = $user->id;
		$data['postal_code']            = $request->postal_code;
		$data['national_code']            = $request->national_code;

//        if ($gateway) {
//            $data['gateway_id']         = $gateway->id;
//        }

//        $carrier_result = $cart->canUseCarrier($request->carrier_id, $request->city_id);

//        if ($cart->hasPhysicalProduct() && !$carrier_result['status']) {
//            return redirect()->back()->withInput()->withErrors([
//                'carrier_id' => $carrier_result['message'],
//            ]);
//        }

		
        $order = Order::create($data);

        //add cart products to order
		$api_data=[];
		$api_data['orderVal.OrderTitle.FldMobile']=$order->mobile;
		$api_data['orderVal.OrderTitle.FldTotalFaktor']=$order->price;
		$api_data['orderVal.OrderTitle.FldTakhfifVizhe']='0';
		$api_data['orderVal.OrderTitle.FldTozihFaktor']='خرید';
		$api_data['orderVal.OrderTitle.FldAddress']=auth()->user()->address;
		$api_data['orderVal.OrderTitle.FldPayId']='0';
		$i=0;
        foreach ($cart->products as $product) {

            $price = $product->prices()->find($product->pivot->price_id);

            if ($price) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://webcomapi.ir/api/Store/GetArticleByCode',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('articleCode' => $product->code),
                    CURLOPT_HTTPHEADER => array(
                        'apiKey: 7a2a1be2*d422*4d70*8b61*affdde'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
				

                $article = json_decode($response)->article;
				if($article){
					$api_data['orderVal.OrderDetails['.$i.'].FldC_Kala']=$article->fldC_Kala;
					$api_data['orderVal.OrderDetails['.$i.'].FldN_Kala']=$article->fldN_Kala;
					$api_data['orderVal.OrderDetails['.$i.'].FldFee']=$article->fldFee;
					$api_data['orderVal.OrderDetails['.$i.'].FldFeeBadAzTakhfif']=$article->fldFeeBadAzTakhfif;
					$api_data['orderVal.OrderDetails['.$i.'].FldN_Vahed']='عدد';
					$api_data['orderVal.OrderDetails['.$i.'].FldN_Vahed_Kol']='';
					$api_data['orderVal.OrderDetails['.$i.'].FldTedad']=$product->pivot->quantity;
					$api_data['orderVal.OrderDetails['.$i.'].FldTedadKol']='0';
					$api_data['orderVal.OrderDetails['.$i.'].FldTedadDarKarton']='0';
					$api_data['orderVal.OrderDetails['.$i.'].FldTozihat']=$order->province .' '.$order->city.' '.$order->address;
					$api_data['orderVal.OrderDetails['.$i.'].FldACode_C']=$article->fldACode_C;
				}
                $order->items()->create([
                    'product_id'      => $product->id,
                    'title'           => $product->title,
                    'price'           => $price->discountPrice(),
                    'real_price'      => $price->tomanPrice(),
                    'quantity'        => $product->pivot->quantity,
                    'discount'        => $price->discount,
                    'price_id'        => $product->pivot->price_id,
                    'code'            => array_key_exists('orderId',json_decode($response,true)) ? json_decode($response,true)['orderId'] : null
                ]);
				$i++;
            }
        }
		$registerUserResponse = Http::withHeaders([
            'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde'
        ])->asForm()->post('https://webcomapi.ir/api/Store/RegisterUser2', [
            'fullName' => $user->first_name." ".$user->last_name,
            'address' => $order->address,
			'state'=>$order->province->name,
			'city'=>$order->city->name,
			'phoneNumber' => strlen(trim($user->mobile)) ? trim($user->mobile) : $user->username,
			'NationalCode' => $user->notional_code,
            'createDate' => time()
        ]);
		if($registerUserResponse->successful()){
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://webcomapi.ir/api/Order/Order',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $api_data, // Article Manual Code
				CURLOPT_HTTPHEADER => array(
					'apiKey: 7a2a1be2*d422*4d70*8b61*affdde'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			$order->items()->update(['code'=>json_decode($response,true)['orderId']]);
		}

        $cart->delete();
        event(new OrderPaid($order));
        return redirect()->route('front.orders.show', ['order' => $order])->with('message', 'ok');
    }
	/*
	
	array(
//                        'orderVal.OrderTitle.FldMobile' => auth()->user()->username, // User Number
                        'orderVal.OrderTitle.FldMobile' => $order->mobile, // User Number
                        'orderVal.OrderTitle.FldTotalFaktor' => $order->price, // sum Amount
                        'orderVal.OrderTitle.FldTakhfifVizhe' => '0', // discount
                        'orderVal.OrderTitle.FldTozihFaktor' => 'خرید', // Order Comment
                        'orderVal.OrderTitle.FldAddress' => auth()->user()->address, // Address
                        'orderVal.OrderTitle.FldPayId' => '0', // PayId For Transaction Id
                        'orderVal.OrderDetails[0].FldC_Kala' => $article->fldC_Kala, // Article System Code
                        'orderVal.OrderDetails[0].FldN_Kala' => $article->fldN_Kala, // Article Name
                        'orderVal.OrderDetails[0].FldFee' => $article->fldFee, // The price of one
                        'orderVal.OrderDetails[0].FldFeeBadAzTakhfif' => $article->fldFeeBadAzTakhfif, //The price of one With Discount
                        'orderVal.OrderDetails[0].FldN_Vahed' => 'عدد', // Unit
                        'orderVal.OrderDetails[0].FldN_Vahed_Kol' => '',
                        'orderVal.OrderDetails[0].FldTedad' => $product->pivot->quantity, // Count
                        'orderVal.OrderDetails[0].FldTedadKol' => '0', // Pack Count
                        'orderVal.OrderDetails[0].FldTedadDarKarton' => '0', // InPack Count
                        'orderVal.OrderDetails[0].FldTozihat' => $order->province .' '.$order->city.' '.$order->address, // Order Comment
                        'orderVal.OrderDetails[0].FldACode_C' => $article->fldACode_C)
	
	*/

    public function pay(Order $order, Request $request)
    {
        if ($order->user_id != auth()->user()->id) {
            abort(404);
        }

        if ($order->status != 'unpaid') {
            return redirect()->route('front.orders.show', ['order' => $order])->with('error', 'سفارش شما لغو شده است یا قبلا پرداخت کرده اید');
        }

        if ($order->price == 0) {
            return $this->orderPaid($order);
        }

        $gateways = Gateway::active()->pluck('key')->toArray();

        $request->validate([
            'gateway' => 'required|in:wallet,' . implode(',', $gateways)
        ]);

        $gateway = $request->gateway;

        if ($gateway == 'wallet') {
            return $this->payUsingWallet($order);
        }

        try {

            $gateway_configs = get_gateway_configs($gateway);

            return Payment::via($gateway)->config($gateway_configs)->callbackUrl(route('front.orders.verify', ['gateway' => $gateway]))->purchase(
                (new Invoice)->amount(intval($order->price)),
                function ($driver, $transactionId) use ($order, $gateway) {
                    DB::table('transactions')->insert([
                        'status'               => false,
                        'amount'               => $order->price,
                        'factorNumber'         => $order->id,
                        'mobile'               => auth()->user()->username,
                        'message'              => 'تراکنش ایجاد شد برای درگاه ' . $gateway,
                        'transID'              => (string) $transactionId,
                        'token'                => (string) $transactionId,
                        'user_id'              => auth()->user()->id,
                        'transactionable_type' => Order::class,
                        'transactionable_id'   => $order->id,
                        'gateway_id'           => Gateway::where('key', $gateway)->first()->id,
                        "created_at"           => Carbon::now(),
                        "updated_at"           => Carbon::now(),
                    ]);

                    session()->put('transactionId', (string) $transactionId);
                    session()->put('amount', $order->price);
                }
            )->pay()->render();
        } catch (Exception $e) {
            return redirect()
                ->route('front.orders.show', ['order' => $order])
                ->with('transaction-error', $e->getMessage())
                ->with('order_id', $order->id);
        }
    }

    public function verify($gateway)
    {
        $transactionId = session()->get('transactionId');
        $amount = session()->get('amount');

        $transaction = Transaction::where('status', false)->where('transID', $transactionId)->firstOrFail();

        $order = $transaction->transactionable;

        $gateway_configs = get_gateway_configs($gateway);

        try {
            $receipt = Payment::via($gateway)->config($gateway_configs);

            if ($amount) {
                $receipt = $receipt->amount(intval($amount));
            }

            $receipt = $receipt->transactionId($transactionId)->verify();

            DB::table('transactions')->where('transID', (string) $transactionId)->update([
                'status'               => 1,
                'amount'               => $order->price,
                'factorNumber'         => $order->id,
                'mobile'               => $order->mobile,
                'traceNumber'          => $receipt->getReferenceId(),
                'message'              => $transaction->message . '<br>' . 'پرداخت موفق با درگاه ' . $gateway,
                'updated_at'           => Carbon::now(),
            ]);

            return $this->orderPaid($order);
        } catch (\Exception $exception) {

            DB::table('transactions')->where('transID', (string) $transactionId)->update([
                'message'              => $transaction->message . '<br>' . $exception->getMessage(),
                "updated_at"           => Carbon::now(),
            ]);

            return redirect()->route('front.orders.show', ['order' => $order])->with('transaction-error', $exception->getMessage());
        }
    }

    private function payUsingWallet(Order $order)
    {
        $wallet  = $order->user->getWallet();
        $amount  = intval($wallet->balance() - $order->price);

        if ($amount >= 0) {
            $result = $order->payUsingWallet();

            if ($result) {
                return $this->orderPaid($order);
            }
        }

        $gateway = Gateway::active()->orderBy('ordering')->first();
        $amount  = abs($amount);

        if (!$gateway) {
            return redirect()->route('front.orders.show', ['order' => $order])
                ->with('transaction-error', 'درگاه فعالی برای پرداخت یافت نشد')
                ->with('order_id', $order->id);
        }

        $history = $wallet->histories()->create([
            'type'        => 'deposit',
            'amount'      => $amount,
            'description' => 'شارژ آنلاین کیف پول برای ثبت سفارش',
            'source'      => 'user',
            'status'      => 'fail',
            'order_id'    => $order->id
        ]);

        try {
            $gateway         = $gateway->key;
            $gateway_configs = get_gateway_configs($gateway);

            return Payment::via($gateway)->config($gateway_configs)->callbackUrl(route('front.wallet.verify', ['gateway' => $gateway]))->purchase(
                (new Invoice)->amount($amount),
                function ($driver, $transactionId) use ($history, $gateway, $amount) {
                    DB::table('transactions')->insert([
                        'status'               => false,
                        'amount'               => $amount,
                        'factorNumber'         => $history->id,
                        'mobile'               => auth()->user()->username,
                        'message'              => 'تراکنش ایجاد شد برای درگاه ' . $gateway,
                        'transID'              => $transactionId,
                        'token'                => $transactionId,
                        'user_id'              => auth()->user()->id,
                        'transactionable_type' => WalletHistory::class,
                        'transactionable_id'   => $history->id,
                        'gateway_id'           => Gateway::where('key', $gateway)->first()->id,
                        "created_at"           => Carbon::now(),
                        "updated_at"           => Carbon::now(),
                    ]);

                    session()->put('transactionId', $transactionId);
                    session()->put('amount', $amount);
                }
            )->pay()->render();
        } catch (Exception $e) {
            return redirect()->route('front.orders.show', ['order' => $order])
                ->with('transaction-error', $e->getMessage())
                ->with('order_id', $order->id);
        }
    }

    private function orderPaid(Order $order)
    {
        $order->update([
            'status' => 'paid',
        ]);

        event(new OrderPaid($order));

        return redirect()->route('front.orders.show', ['order' => $order])->with('message', 'ok');
    }
}
