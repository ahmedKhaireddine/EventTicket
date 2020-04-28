<?php

namespace App\Http\Controllers;

use App\Actions\ActivateEventAction;
use App\Event;

class ActivateEventController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Event $event)
    {
        $response = (new ActivateEventAction)->execute($event);

        return response()->json([
            'message' => $response
        ], 200);
    }
}
