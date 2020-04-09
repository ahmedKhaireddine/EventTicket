<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationStoreRequest;
use App\Http\Resources\ConversationCollection;
use App\Http\Resources\ConversationResource;
use App\Message;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * The user model implementation.
     *
     * @var App\User
     */
    protected $userConnected;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userConnected = Auth::guard('api')->user();
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\ConversationCollection
     */
    public function index(Request $request)
    {
        $validate = $request->validate([
            'user_id' => 'required|exists:users,id|integer',
        ]);

        $this->authorize('to-speak', $request->input('user_id'));

        $from_user = User::find($request->input('user_id'));

        if ($from_user->hasUnreadMessages()) {
            $from_user->readAllMessages();
        }

        $messages = Message::getConversation($this->userConnected->id, $from_user->id)->get();

        return new ConversationCollection($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ConversationStoreRequest  $request
     * @return \App\Http\Resources\ConversationResource
     */
    public function store(ConversationStoreRequest $request)
    {
        $message = Message::create([
            'content' => $request->content,
            'create_at' => Carbon::now(),
            'from_id' => $this->userConnected->id,
            'to_id' => $request->to_id,
        ]);

        return new ConversationResource($message);
    }
}
