<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string  $lang
     * @return \Illuminate\Http\Response
     */
    public function __invoke(string $lang)
    {
        App::setLocale($lang);

        return response()->json([
            'message' => trans('Successful change of language.')
        ], 200);
    }
}
