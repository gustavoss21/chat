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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $index_message = Message::Create($request->all());

        if(!$index_message->count() > 0){
            return response('the message no was created',400);
        }

        return response('message created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        return response($message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }

    public function messages(Request $request){

        $messages = Message::where('sender_user_id',$request->get('sender_user_id'))
                ->where('recipient_user_id',$request->get('recipient_user_id'))
                ->orWhere(function($query) use($request){
                    $query->where('recipient_user_id',$request->get('sender_user_id'))
                    ->where('sender_user_id',$request->get('recipient_user_id'));
                })
                ->get();
        
        return response()->json($messages);

    }

    public function sendMessage(Request $request){
        $message = Message::create($request->all());
        return response()->json($message);
    }
}
