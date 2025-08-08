<?php

namespace App\Http\Controllers;

use App\Repositories\TrackingRepository;

class TrackingController extends Controller
{
    protected TrackingRepository $instance;

    public function __construct(TrackingRepository $instance)
    {
        $this->instance = $instance;
    }

    public function index()
    {
        return response()->json([
            "values" => $this->instance->fetchByBranchAndYear()
        ]);
    }

}
