<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Country;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::paginate(10);
        
        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get(); // Fetch all countries
        $defaultCurrency = Currency::where('is_default', true)->first();
        return view('currencies.create', compact('defaultCurrency', 'countries'));
    }

    
        public function store(Request $request)
        {
            $request->validate([
                'country_id' => 'required|exists:countries,id',
                'currency_code' => 'required|string|unique:currencies,currency_code',
                'currency_symbol' => 'required|string',
                'conversion_rate' => 'required|numeric|min:0',
                'is_default' => 'sometimes|boolean',
            ]);
        
            $isDefault = $request->has('is_default') ? true : false;
            
            if ($isDefault) {
                // Unset existing default currency
                Currency::where('is_default', true)->update(['is_default' => false]);
                session()->flash('warning', 'Previous default currency has been updated.');
            }
        
            Currency::create($request->all());
        
            return response()->json(['success' => true, 'message' => 'Currency added successfully!']);
        }
        
    

    public function edit(Currency $currency)
    {
        $countries = Country::orderBy('name')->get();
        $defaultCurrency = Currency::where('is_default', true)->first();
        return view('currencies.edit', compact('currency', 'defaultCurrency', 'countries'));
    }

    
        public function update(Request $request, Currency $currency)
        {
            
            $request->validate([
                // 'country_id' => 'required|exists:countries,id',
                'currency_code' => 'required|string|unique:currencies,currency_code,' . $currency->id,
                'currency_symbol' => 'required|string',
                'conversion_rate' => 'required|numeric|min:0',
                'is_default' => 'sometimes|boolean',
            ]);
            // Only update default if explicitly checked
            if ($request->boolean('is_default')) {
                // Set all others to false
                Currency::where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
                $currency->is_default = true;
                session()->flash('warning', 'Previous default currency has been updated.');
            }

            // If not present in the request, don't change is_default
            $updateData = $request->only(['currency_code', 'currency_symbol', 'conversion_rate']);
            
             $currency->update($updateData);
          
            return response()->json(['success' => true, 'message' => 'Currency updated successfully!']);
        }
        
        public function getTopCurrencies($currentCurrencyId)
{
    $currencies = Currency::where('id', '!=', $currentCurrencyId)
        ->orderByDesc('conversion_rate')
        ->take(5)
        ->get();

    return response()->json($currencies);
}


        public function updateConversionRates(Request $request)
{
    foreach ($request->conversion_rates as $id => $rate) {
        Currency::where('id', $id)->update(['conversion_rate' => $rate]);
    }

    return response()->json(['success' => true, 'message' => 'Conversion rates updated!']);
}

        

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted successfully!');
    }
}
