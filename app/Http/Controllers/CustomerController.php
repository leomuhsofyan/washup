<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ClaimedVoucher;
use App\Models\Customer; // Asumsikan Anda memiliki model Customer
use App\Models\User;

class CustomerController extends Controller
{
    use HttpResponses;
    public function getCustomer(Request $request)
    {
        $userId = $request->user()->id;

        // Mendapatkan data customer berdasarkan user_id
        $customer = Customer::where('user_id', $userId)->first();

        // Jika customer tidak ditemukan, kembalikan response error
        if (!$customer) {
            return $this->error('', 'Credentials do not match', 401);
        }
        // dd($userId);
        // Menghitung jumlah voucher berdasarkan user_id
        $voucherCount = ClaimedVoucher::where('user_id', $userId)->count();

        // Menambahkan jumlah voucher ke dalam response
        $customerData = $customer->toArray();
        $customerData['voucher_count'] = $voucherCount;

        // Jika customer ditemukan, kembalikan data customer
        return response()->json([
            'status' => 'Request was successful.',
            'message' => null,
            'data' => $customerData
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->user()->id;
        $customer = Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return $this->error('', 'Customer not found', 404);
        }

        $user = User::find($userId);
        if (!$user) {
            return $this->error('', 'User not found', 404);
        }

        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $user->password = $request->input('password');
        $user->save();

        $customer->name = $request->input('name');
        $customer->contact = $request->input('contact');
        $customer->address = $request->input('address');
        $customer->save();

        return $this->success($customer);
    }
}