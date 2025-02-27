<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function update(Request $request) {
        $validatedFields = $request->validate([
            "order_id" => "required",
            "status" => "required"
        ]);

        $order = Order::find($validatedFields["order_id"]);

        $payment = Payment::find($order->payment->id);

        $payment->update([
            "status" => $validatedFields["status"]
        ]);

        return response(["message" => "payment updated"]);
    }
}
