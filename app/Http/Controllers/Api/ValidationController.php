<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ValidationController extends Controller
{
    public function validateGST($gst)
    { 
        if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $gst)) {
            return response()->json(['valid' => false, 'message' => 'Invalid GST Format']);
        }

        try {
            $response = Http::withHeaders([
                'client_id'     => config('services.gst.client_id'),
                'client_secret' => config('services.gst.client_secret'),
            ])->get("https://api.mastergst.com/gst/public/$gst");

            if ($response->successful() && isset($response['data'])) {
                return response()->json([
                    'valid' => true,
                    'legal_name' => $response['data']['lgnm'] ?? 'Unknown'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => 'GST API error']);
        }

        return response()->json(['valid' => false, 'message' => 'Invalid GST']);
    }

    public function validatePAN($pan)
    {   
        if (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan)) {
            return response()->json(['valid' => false, 'message' => 'Invalid PAN Format']);
        }

        try {
            $response = Http::withToken(config('services.pan.api_key'))
                ->get("https://your-pan-api.com/verify/$pan");

            if ($response->successful() && isset($response['data'])) {
                return response()->json([
                    'valid' => true,
                    'holder' => $response['data']['full_name'] ?? 'Unknown'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => 'PAN API error']);
        }

        return response()->json(['valid' => false, 'message' => 'Invalid PAN']);
    }


    // public function validateGST($gst)
    // {
    //     // Basic regex validation
    //     if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $gst)) {
    //         return response()->json(['valid' => false, 'message' => 'Invalid GST Format']);
    //     }

    //     // 👇 Demo response for development
    //     return response()->json([
    //         'valid' => true,
    //         'legal_name' => 'Demo Pvt Ltd',
    //         'gst' => $gst,
    //         'state' => 'West Bengal'
    //     ]);
    // }

    // public function validatePAN($pan)
    // {
    //     if (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan)) {
    //         return response()->json(['valid' => false, 'message' => 'Invalid PAN Format']);
    //     }

    //     // 👇 Demo response for development
    //     return response()->json([
    //         'valid' => true,
    //         'holder' => 'John Doe',
    //         'pan' => $pan
    //     ]);
    // }
}
