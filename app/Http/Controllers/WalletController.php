<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $transactions = $user->walletTransactions()->paginate(15);

        return view('wallet.index', compact('user', 'transactions'));
    }

    public function topup(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000|max:2000000',
            'method' => 'required|string',
        ]);

        $amount = (int) $validated['amount'];
        $method = $validated['method'];
        $methodMap = [
            'QRIS' => 'qris',
            'BCA' => 'tf_bca',
            'BRI' => 'tf_bri',
            'Mandiri' => 'tf_mandiri',
            'BNI' => 'tf_bni',
        ];
        $paymentMethod = $methodMap[$method] ?? (str_starts_with($method, 'tf_') || $method === 'qris' ? $method : 'qris');

        $order = app(PaymentController::class)->createOrder(
            user: $user,
            purpose: 'topup',
            amount: $amount,
            paymentMethod: $paymentMethod,
            itemName: 'Top-up Saldo Dompet Bahari',
        );

        return redirect()->route('payments.checkout', $order->order_id);
    }
}
