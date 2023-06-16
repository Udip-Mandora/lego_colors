<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Color;
use Exception;

class ColorApiController extends Controller
{
    public function index()
    {
        $apiKey = '8bb5ff20c88d9f34e0aab9189e3d5bfa';

        // $response = Http::withHeaders([
        //     'Authorization' => 'key ' . $apiKey,
        // ])->get('https://rebrickable.com/api/v3/lego/colors');

        $response = Http::withOptions(['verify' => false])->get('https://rebrickable.com/api/v3/lego/colors/?page_size=300&key=' . $apiKey);
        $data = json_decode($response);
        $data = $data->results;
        echo '<pre>';
        // print_r($data);
        // dd($data);
        // return view('/colors/index', ['data' => $data->results]);
        $ids = [];
        $name = [];
        $rgb = [];
        $is_trans = [];
        $brickId = [];


        foreach ($data as $item) {
            if ($item->id >= 0) {
                echo 'ID: ' . $item->id . '<br>';
                echo 'Name: ' . $item->name . '<br>';
                echo 'RGB: #' . $item->rgb . '<br>';



                echo '<div style="width: 100px; height: 100px; background-color:#' . $item->rgb . ';"></div>';

                if (property_exists($item->external_ids, 'BrickOwl')) {

                    $brick_owl = $item->external_ids->BrickOwl;

                    if (count($brick_owl->ext_ids)) {
                        foreach ($brick_owl->ext_ids as $key => $value) {
                            echo 'BrickOwl Name: ' . $brick_owl->ext_ids[$key] . ' - ' . $brick_owl->ext_descrs[$key][0] . '<br>';
                        }
                    }
                } else {
                    echo 'NO BRICKOWL ALTERNATIVE NAME<br>';
                }

                if (property_exists($item->external_ids, 'BrickLink')) {
                    $brick_link = $item->external_ids->BrickLink;

                    if (count($brick_link->ext_ids)) {
                        foreach ($brick_link->ext_ids as $key => $value) {
                            echo 'BrickLink Name: ' . $brick_link->ext_ids[$key] . '-' . $brick_link->ext_descrs[$key][0] . '<br>';
                        }
                    } else {
                        echo 'NO BRICKLINK ALTEERNATIVE NAME<BR>';
                    }
                }

                if (property_exists($item->external_ids, 'LEGO')) {

                    $lego = $item->external_ids->LEGO;

                    if (count($lego->ext_ids)) {
                        foreach ($lego->ext_ids as $key => $value) {
                            echo 'LEGO Name: ' . $lego->ext_ids[$key] . ' - ' . $lego->ext_descrs[$key][0] . '<br>';
                        }
                    }
                } else {
                    echo 'NO LEGO ALTERNATIVE NAME<br>';
                }

                echo '<hr>';
                // dd($lego->ext_descrs[1][1]);

                // print_r($lego_desc);


                print_r($brick_link);
                print_r($lego);

                $lego_id =  $lego->ext_ids[0];
                $lego_desc = $lego->ext_descrs[0][0];
                if (substr($lego_desc, 0, 7) == 'CONDUCT') {
                    $lego_id =  $lego->ext_ids[1];
                    $lego_desc = $lego->ext_descrs[1][0];
                }

                print_r($lego_desc);
                // die();

                // try {
                Color::updateOrCreate([
                    'name' => $item->name,
                    'rgb' => $item->rgb,
                    'transparency' => $item->is_trans,
                    'brickLinkExtId' => $brick_link->ext_ids[0],
                    'brickLinkExtDesc' => $brick_link->ext_descrs[0][0],
                    'LegoExtId' => $lego_id,
                    'LegoExtDesc' => $lego_desc,
                ]);
                // } catch (Exception $e) {
                //     dd($lego);
                // }

                $ids[] = $item->id;
                $name[] = $item->name;
                // print_r($name);

            }

            // dd($ids);
            // $brick_link;
            // if (property_exists($item->external_ids, 'BrickLink') & property_exists($item->external_ids, 'LEGO')) {
            //     $brick_link = $item->external_ids->BrickLink;
            //     $link = $brick_link->ext_ids;
            //     $desc = $brick_link->ext_descrs[0];
            //     print_r($link);
            //     print_r($desc);

            //     $lego = $item->external_ids->LEGO;
            //     $lid = $lego->ext_ids;
            //     $ldesc = $lego->ext_descrs[0];
            //     $ldes = implode(',', $ldesc);
            //     print_r($lid);
            //     print_r($ldes);

            // Color::updateOrCreate([
            //     'name' => $item->name,
            //     'rgb' => $item->rgb,
            //     'transparency' => $item->is_trans,
            //     'brickLinkExtId' => $link,
            //     'brickLinkExtDesc' => $desc,
            //     'LegokExtId' => $lid,
            //     'LegoExtDesc' => $ldes,
            // ]);
        }
        die();



        // Color::updateOrCreate([
        //     'name' => $item->name,
        //     'rgb' => $item->rgb,
        //     'transparency' => $item->is_trans,
        //     'brickLinkExtId' => $item->external_ids->BrickLink->ext_ids,
        //     'brickLinkExtDesc' => $item->ext_descrs,
        //     'LegokExtId' => $item->ext_ids,
        //     'LegoExtDesc' => $item->ext_descrs,
        // ]);




        // foreach ($data as $item) {
        //     // $extIds = $item->external_ids->LEGO->ext_ids;

        //     $item = (array)$item;
        //     if ($item['id'] >= 0) {
        //         $ids[] = $item['id'];
        //         $name[] = $item['name'];
        //         $rgb[] = $item['rgb'];
        //         $id = (int)$ids;
        //         $is_trans[] = $item['is_trans'];
        //         // $brickId[] = $item['external_ids']['BrickLink']->ext_ids;
        //         // $brickId[] = $item['external_ids']->BrickLink->ext_ids;
        //         // $brickId[] = is_array($item) ? $item['external_ids']['BrickLink']['ext_ids'] : $item->external_ids->BrickLink->ext_ids;
        //         // $extIds = $item['external_ids']->BrickLink->ext_ids;
        //     }
        //     $id = $item['id'];
        //     // $brickId[] = $item['$id' + 1]->external_ids;
        // }
        // print_r($name);
        // foreach ($data as $item) {
        //     $id = is_array($item) ? $item['id'] : $item->id;
        //     if ($id >= 1) {
        //         print_r($id);
        //         $brickLinkIds[] = is_array($item)
        //             ? $item['external_ids']['BrickLink']['ext_ids']
        //             : $item->external_ids->BrickLink->ext_ids;
        //     }
        // }

        // print_r($ids);
        // print_r($brickId);
        // $name = $data[]->exter;
        // $externalIds = $data[$id]->external_ids->BrickLink->ext_ids;
        // $extIds = $data[1]->external_ids->LEGO->ext_ids;
        // $legoDes = $data[1]->external_ids->LEGO->ext_descrs;
        // print_r($externalIds);
        // print_r($brickId);
        // echo "<pre>";
        // foreach ($data as $item) {

        //     $item = (array)$item;
        // print_r($item);
        // $extIds = $data->results[0]->external_ids->BrickOwl->ext_ids;

        // Color::updateOrCreate([
        //     'name' => $item['name'],
        //     'rgb' => $item['rgb'],
        //     'transparency' => $item['is_trans'],
        //     'brickLinkExtId' => $item['ext_ids'],
        //     'brickLinkExtDesc' => $item['ext_descrs'],
        //     'LegokExtId' => $item['ext_ids'],
        //     'LegoExtDesc' => $item['ext_descrs'],
        // ]);
    }
    // dd("data stored");
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
