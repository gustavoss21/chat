<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $messages = Message::where('sender_user_id',$request->get('sender_user_id'))
        ->where('recipient_user_id',$request->get('recipient_user_id'))
        ->orWhere(function($query) use($request){
            $query->where('recipient_user_id',$request->get('sender_user_id'))
            ->where('sender_user_id',$request->get('recipient_user_id'));
        })
        ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $message = Message::Create($request->all());

        return response()->json($message);
    }
}
