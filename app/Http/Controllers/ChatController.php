<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\SendMessage;
use App\Events\MessageViewed;
use App\Models\UserContactChat;
use App\Events\AlertsArrivalMainUser;
use App\Events\AlertArrivalUser;
use App\Events\UserConnectionConfirmation;
use App\Events\CallClosedEvent;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;


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
        $message1 = new Message($request->all());
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

    public function activeCall(Request $request){
        $user_id = $request->get('id');
        $user_call = UserContactChat::firstOrCreate(['user_id'=>$user_id]);    

        AlertArrivalUser::dispatch($user_id);

        return response()->json($user_call);
    }

    public function activeMainCall(Request $request){
        $user = $request->only('user_id');
        $call_main = UserContactChat::where('user_id',$user['user_id'])->first();
        
        if($call_main->user_main_id){
            return response()->json(['status'=>'error','message'=>'main user already logged in'],404);
        }

        AlertsArrivalMainUser::dispatch($user['user_id'],Auth::id());
        
        $call_main->user_main_id = Auth::id();
        $call = $call_main->update();

        return response()->json(['was_updated_'=>$call]);
    }
    public function callUsers(){
        $users_For_call = UserContactChat::whereNull('user_main_id')->get();

        return response()->json($users_For_call);
    }

    public function userConect(Request $request){
        $user_auth = Auth::user();
        // $user_id = $request->get('user_id');
        $super_user_id = $request->get('super_user_id');

        $call = UserContactChat::where('user_id',$user_auth->id)->delete();

        //transmition
        UserConnectionConfirmation::dispatch($user_auth->id,$super_user_id);

        return Response()->json(['status_call'=>'confirmed',$call]);
    }

    public function dropCall(Request $request){
        $user_auth = Auth::user();
        $user_id = $request->get('user_id');
        $super_user_id = $request->get('super_user_id');
        $user_receive = $request->get('user_receive');

        if(!in_array($user_auth->id,[$super_user_id,$user_id])){
            return Response()->json(['status_call'=>'not closed'],401);
        }

        $call = UserContactChat::where('user_id',$user_id)->remove();

        //transmition
        CallClosedEvent::dispatch($user_auth->id,$user_receive);

        return Response()->json(['status_call'=>'closed',$call]);
    }
}
