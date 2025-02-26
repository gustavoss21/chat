<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\SendMessage;
use App\Events\MessageViewed;

use function PHPSTORM_META\map;

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
        SendMessage::dispatch($message);
        return response()->json($message);
    }

    public function updateViews(Request $request)
    {
        $request_filter = $request->all();

        array_pop($request_filter);

        if(empty($request_filter)){
            return response()->json(['status'=>500,'message'=>'houve um error insperador']);

        }

        $messages_id_viewed = array_map(function($item){return $item['id'];},$request_filter);
        $chats = Message::whereIn('id', $messages_id_viewed)->update(['status' =>Message::STATUS_VIEWED]);

        if($chats < 1){
            return response()->json(['status'=>500,'message'=>'houve um error insperador']);
        }

        MessageViewed::dispatch($messages_id_viewed,$request_filter[0]['sender_user_id']);

        return response()->json(['status'=>200,'message'=>'updated']);
    }
}
