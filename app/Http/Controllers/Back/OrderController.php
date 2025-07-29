<?php

namespace App\Http\Controllers\Back;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Datatable\Order\OrderCollection;
use App\Models\Order;
use App\Models\Price;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index()
    {
        return view('back.orders.index');
    }

    public function apiIndex(Request $request)
    {
        $this->authorize('orders.index');

        $orders = Order::filter($request);

        $orders = datatable($request, $orders);

        return new OrderCollection($orders);
    }

    public function show(Order $order)
    {
        return view('back.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->transactions()->delete();

        $order->delete();
        toastr()->success('سفارش با موفقیت حذف شد.');

        return redirect()->route('admin.orders.index');
    }

    public function multipleDestroy(Request $request)
    {
        $this->authorize('orders.delete');

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);

        foreach ($request->ids as $id) {
            $order = Order::find($id);
            $this->destroy($order);
        }

        return response('success');
    }

    public function shipping_status(Order $order, Request $request)
    {
        $this->authorize('orders.update');

        $this->validate($request, [
            'status' => 'required',
        ]);

        $order->update([
            'shipping_status' => $request->status
        ]);

        return response('success');
    }

    public function notCompleted()
    {
        $this->authorize('orders.index');

        $prices = Price::whereHas('orderItems', function ($q) {
            $q->whereHas('order', function ($q2) {
                $q2->notCompleted();
            })->whereHas('product', function ($q3) {
                $q3->physical();
            });
        })->paginate(20);

        return view('back.orders.not-completed', compact('prices'));
    }

    public function print(Order $order)
    {
        $this->authorize('orders.view');

        return view('back.orders.print', compact('order'));
    }

    public function shippingForm(Order $order)
    {
        $this->authorize('orders.view');

        return view('back.orders.shipping-form', compact('order'));
    }

    public function export(Request $request)
    {
        $this->authorize('orders.export');

        $orders = Order::filter($request)->get();

        switch ($request->export_type) {
            case 'excel': {
                    return $this->exportExcel($orders, $request);
                    break;
                }
            default: {
                    return $this->exportPrint($orders, $request);
                }
        }
    }

    private function exportExcel($orders)
    {
        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }
}
