<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ProcessCampaign;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign = Campaign::create(['name' => $request->name]);

        $filePath = $request->file('csv_file')->store('campaigns');

        ProcessCampaign::dispatch($campaign, $filePath);

        return response()->json(['message' => 'Campaign created and processing started.'], 201);
    }
}
