<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Color;

class ColorApiController extends Controller
{
    public function index()
    {
        $apiKey = '8bb5ff20c88d9f34e0aab9189e3d5bfa';

        // $response = Http::withHeaders([
        //     'Authorization' => 'key ' . $apiKey,
        // ])->get('https://rebrickable.com/api/v3/lego/colors');

        $response = Http::withOptions(['verify' => false])->get('https://rebrickable.com/api/v3/lego/colors/?key=' . $apiKey);
        $data = json_decode($response);
        // dd($data);
        return view('/colors/index', ['data' => $data->results]);

        foreach ($data as $item) {
            Color::create([
                'name' => $item['name'],
                'rgb' => $item['rgb'],
                'transparency' => $item['transparency'],
                'brickLinkExtId' => $item['brickLinkExtId'],
                'brickLinkExtDesc' => $item['brickLinkExtDesc'],
                'LegokExtId' => $item['LegoExtId'],
                'LegoExtDesc' => $item['LegoExtDesc'],
            ]);
        }
    }


    // public function index()
    // {
    //     // Make a request to the Rebrickable API to fetch colors data
    //     $response = file_get_contents('https://api.rebrickable.com/v3/colors', false, stream_context_create([
    //         'http' => [
    //             'method' => 'GET',
    //             'header' => 'Authorization: Bearer 8bb5ff20c88d9f34e0aab9189e3d5bfa', // Replace with your actual API key
    //         ],
    //     ]));

    //     // Check if the request was successful
    //     if ($response === false) {
    //         die('Failed to fetch data from the API.');
    //     }

    //     // Parse the API response into an associative array
    //     $data = json_decode($response, true);

    //     // Check if the response was successfully parsed and contains the expected data structure
    //     if (is_array($data) && isset($data['count'])) {
    //         // Access the 'count' property and display the number of colors
    //         $count = $data['count'];
    //         echo "Total number of colors: " . $count;
    //     } else {
    //         // Handle error response
    //         echo "Error occurred while fetching colors data.";
    //     }

    //     return view('index');
    // }
}
