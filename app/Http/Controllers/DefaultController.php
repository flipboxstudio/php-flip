<?php

namespace App\Http\Controllers;

class DefaultController extends Controller
{
    /**
     * Root API endpoint.
     */
    public function index()
    {
        return [
            'message' => 'You have arrived.',
        ];
    }
}
