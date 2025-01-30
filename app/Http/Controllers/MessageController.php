<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\IndexMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
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
}
