<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Color;
use Exception;

class ColorApiController extends Controller
{
    // function to perform necessary tasks using lego colors api
    public function index()
    {
        // api key to access data from api
        $apiKey = '8bb5ff20c88d9f34e0aab9189e3d5bfa';

        // $response = Http::withHeaders([
        //     'Authorization' => 'key ' . $apiKey,
        // ])->get('https://rebrickable.com/api/v3/lego/colors');

        // declaring a way to fetch the data from api overtaking the verifying of api
        $response = Http::withOptions(['verify' => false])->get('https://rebrickable.com/api/v3/lego/colors/?page_size=300&ordering=name&key=' . $apiKey);

        // coverting data into json object
        $data = json_decode($response);

        //getting into a property called results to fetch necessary data
        $data = $data->results;

        //printing it
        echo '<pre>';

        //declaring empty array to store data into it
        $ids = [];
        $name = [];
        $rgb = [];
        $is_trans = [];
        $brickId = [];


        // creating a loop to iteratre through the whole data and get every piece
        foreach ($data as $item) {

            //setting id to 0 instead of -1 so we get data from id 0
            if ($item->id >= 0) {
                //priting id, name and rgb pattern from the api
                echo 'ID: ' . $item->id . '<br>';
                echo 'Name: ' . $item->name . '<br>';
                echo 'RGB: #' . $item->rgb . '<br>';


                // adding a styling portion so we can see live example of color itself
                echo '<div style="width: 100px; height: 100px; background-color:#' . $item->rgb . ';"></div>';

                // checking and making sure if a property called BrickOwl exists or not so we can fetch data from it
                if (property_exists($item->external_ids, 'BrickOwl')) {

                    // storing data from BrickOwl into a variable
                    $brick_owl = $item->external_ids->BrickOwl;

                    // checking if property called ext_ids exist or not 
                    if (count($brick_owl->ext_ids)) {
                        // fetching data from ext_ids using a loop and storing it as a key-value pair
                        foreach ($brick_owl->ext_ids as $key => $value) {
                            // priting id and description
                            echo 'BrickOwl Name: ' . $brick_owl->ext_ids[$key] . ' - ' . $brick_owl->ext_descrs[$key][0] . '<br>';
                        }
                    }
                } else {
                    //if there is no data then this message wiill show up
                    echo 'NO BRICKOWL ALTERNATIVE NAME<br>';
                }

                // checking and making sure if a property called BrickLink exists or not so we can fetch data from it
                if (property_exists($item->external_ids, 'BrickLink')) {
                    // storing data from BrickLink into a variable
                    $brick_link = $item->external_ids->BrickLink;

                    // checking if property called ext_ids exist or not
                    if (count($brick_link->ext_ids)) {
                        // fetching data from ext_ids using a loop and storing it as a key-value pair
                        foreach ($brick_link->ext_ids as $key => $value) {
                            // priting id and description
                            echo 'BrickLink Name: ' . $brick_link->ext_ids[$key] . '-' . $brick_link->ext_descrs[$key][0] . '<br>';
                        }
                    } else {
                        //if there is no data then this message wiill show up
                        echo 'NO BRICKLINK ALTEERNATIVE NAME<BR>';
                    }
                }

                // checking and making sure if a property called LEGO exists or not so we can fetch data from it
                if (property_exists($item->external_ids, 'LEGO')) {
                    // storing data from LEGO into a variable
                    $lego = $item->external_ids->LEGO;

                    // checking if property called ext_ids exist or not
                    if (count($lego->ext_ids)) {
                        // fetching data from ext_ids using a loop and storing it as a key-value pair
                        foreach ($lego->ext_ids as $key => $value) {
                            // priting id and description
                            echo 'LEGO Name: ' . $lego->ext_ids[$key] . ' - ' . $lego->ext_descrs[$key][0] . '<br>';
                        }
                    }
                } else {
                    //if there is no data then this message wiill show up
                    echo 'NO LEGO ALTERNATIVE NAME<br>';
                }

                echo '<hr>';
                print_r($brick_link);
                print_r($lego);

                //fetching id of first index of LEGO property 
                $lego_id =  $lego->ext_ids[0];
                //fetching description of first index and inside that the first index of LEGO property
                $lego_desc = $lego->ext_descrs[0][0];
                // running a condition where if the color description is like ex: "CONDUCT.BLACK" then remove the CONDUCT word and only store the other word
                if (substr($lego_desc, 0, 7) == 'CONDUCT') {
                    $lego_id =  $lego->ext_ids[1];
                    $lego_desc = $lego->ext_descrs[1][0];
                }

                print_r($lego_desc);

                // Storing everything into the database
                Color::updateOrCreate([
                    'name' => $item->name,
                    'rgb' => $item->rgb,
                    'transparency' => $item->is_trans,
                    'brickLinkExtId' => $brick_link->ext_ids[0],
                    'brickLinkExtDesc' => $brick_link->ext_descrs[0][0],
                    'LegoExtId' => $lego_id,
                    'LegoExtDesc' => $lego_desc,
                ]);
            }
        }
    }
}

                                // ANOTHER METHOD FETCH DATA FROM API WITH SOME SORT OF SECURITY 

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
