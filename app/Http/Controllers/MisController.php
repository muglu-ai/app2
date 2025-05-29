<?php

namespace App\Http\Controllers;
//use country and states model
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class MisController extends Controller
{
    //
    //there is two fields country and state in the application form so we need to fetch the country and state from the database and send it to the view
    public function getCountryAndState()
    {
        // Fetch all countries
        $countries = Country::all();
        return view('test.import_states', compact('countries'));
    }

    public function getStates(Request $request)
    {
        if (!$request->has('country_id')) {
            return response()->json(['error' => 'Country ID is missing'], 400);
        }

        //validate the country id and get the states
        $validated = $request->validate([
            'country_id' => 'required|integer',
        ]);



        $states = State::where('country_id', $request->country_id)->get();

        return response()->json($states);
    }


}
