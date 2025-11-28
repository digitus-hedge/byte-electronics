<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerAddress;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Order;

class FrontendProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // dd($user);
        if (!$user) {
            return redirect()->route('login');
        }

        $verified='Unverified❌';
        if($user->email_verified_at){
            $verified='Verified✅';
        }

        $customerAddresses = CustomerAddress::where('customer_id', $user->id)->get();
        $countries = Country::select('id', 'name', 'short_name', 'country_code')->get();
        $tab = $request->query('tab', 'ALL ORDERS');
        $search = $request->query('search');

        $ordersQuery = Order::with(['status', 'user'])
            ->where('user_id', $user->id)
            ->when($search, fn($q) => $q->where('id', 'like', "%{$search}%"))
            ->when($tab !== 'ALL ORDERS', fn($q) => $q->whereHas('status', fn($q) => $q->where('name', $tab)));

        $orders = $ordersQuery->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->order_number ?? '#'.$order->id,
                    'name' => $order->user->name ?? 'Guest',
                    'payment' => $order->payment_status ?? 'Pending',
                    'status' => $order->status->name ?? 'Pending',
                    'total' => number_format($order->total_amount ?? 0, 2),
                ];
            })->toArray();

        return Inertia::render('MyProfile/Profile', [
            'user' => $user,
            'customerAddresses' => $customerAddresses,
            'countries' => $countries,
            'orders' => $orders,
            'tab' => $tab,
            'search' => $search,
            'verified' => $verified,
        ]);
    }

    public function storeAddress(Request $request)
    {
        // dd($request->all());
//         "addressType" => "New Address"
//   "userName" => "digimon"
//   "email" => "admin@gmail.com"
//   "company_name" => "Digitus"
//   "address_line_1" => "vadakara"
//   "address_line_2" => "vo road"
//   "City" => "vadakara"
//   "State" => "kerala"
//   "country" => 5
//   "postal_code" => "123123"
//   "attention" => "sir!yes sir!"
//   "countryCode" => "+91"
//   "phoneNumber" => "74147414714"
//   "phone" => "+91 74147414714"
        $request->validate([
            'addressName'  => 'required|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'postal_code'   => 'required|string|max:20',
            'attention'     => 'nullable|string',
            'phone'         => 'required|string|max:20',
            'country_id'    => 'required|exists:countries,id', //Country ID
            'countryCode'   => 'required|string|max:5',
        ]);

        // $country = Country::findOrFail($request->country_id);
        $user = auth()->user();
        // dd($user->id);
        CustomerAddress::create([
            'customer_id'   => $user->id,
            'address_name'  => $request->addressName,
            'company_name'  => $request->company_name,
            'address_line1' => $request->address_line_1,
            'address_line2' => $request->address_line_2,
            'city'          => $request->city,
            'state'         => $request->state,
            'postal_code'   => $request->postal_code,
            'attention'     => $request->attention,
            'phone'         => $request->phone,
            'country_id'    => $request->country_id,
            'country_code'  => $request->countryCode,
        ]);

        // return back()->with('success', 'Address added successfully.');
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Address added successfully.');


    }

    public function updateAddress(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'addressName'  => 'required|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'postal_code'   => 'required|string|max:20',
            'attention'     => 'nullable|string',
            'phone'         => 'required|string|max:20',
            'country_id'    => 'required|exists:countries,id', //Country ID
            'countryCode'   => 'required|string|max:5',
        ]);
// dd(auth()->user()->id);
        $address = CustomerAddress::where('customer_id', auth()->user()->id)->findOrFail($id);
        $country = Country::findOrFail($request->country_id);
        // dd('address:',$address , 'request:',$request->all());

        $address->update([
            'address_name'  => $request->address_name,
            'company_name'  => $request->company_name,
            'address_line1' => $request->address_line_1,
            'address_line2' => $request->address_line_2,
            'city'          => $request->city,
            'state'         => $request->state,
            'postal_code'   => $request->postal_code,
            'attention'     => $request->attention,
            'phone'         => $request->phone,
            'country_id'    => $request->country_id,
            'country_code'  => $country->CountryCode,
        ]);

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress($id)
    {
        $address = CustomerAddress::where('customer_id', auth()->id())->findOrFail($id);
        $address->delete(); // Soft delete

        return back()->with('success', 'Address deleted successfully.');
    }
}
