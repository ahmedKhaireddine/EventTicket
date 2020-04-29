<?php

namespace App\Http\Controllers;

use App\Actions\PublishEventAction;
use App\Event;


class PublishEventController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Event
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Event $event)
    {
        $response = (new PublishEventAction)->execute($event);

        return response()->json([
            'message' => $response['message']
        ], $response['code']);
    }
}
