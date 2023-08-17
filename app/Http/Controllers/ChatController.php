<?php

namespace App\Http\Controllers;

use App\Events\NewMessageSent;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{

    use ApiResponseTrait;

    //create and check chat
    public function createChat()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $second_user_id = $_GET['id'];

        $prevChat = Chat::query()
            ->where(['user_id' => $user->id , 'second_user_id' => $second_user_id])
            ->first();

        if ($prevChat)
        {
            return $this->apiResponse($prevChat , 'This chat already exist' , 200);
        }

        $chat = Chat::create([
            'user_id' => $user->id,
            'second_user_id' => $second_user_id
        ]);

        return $this->apiResponse($chat , 'chat created successfully!' , 200);
    }


    //get all chat
    public function getChats()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $chats1 = Chat::query()
            ->where(['chats.user_id' => $user->id])
            ->join('users' , 'chats.second_user_id' , 'users.id')
            ->select('users.name as name' , 'users.id as doctor_id_as_user')
            ->orderBy('chats.updated_at' ,'desc')
            ->get()
            ->toArray();

        $chats2 = Chat::query()
            ->Where(['chats.second_user_id' => $user->id])
            ->join('users' , 'chats.user_id' , 'users.id')
            ->select('users.name as name' , 'users.id as doctor_id_as_user')
            ->orderBy('chats.updated_at')
            ->get()
            ->toArray();

        $chats = array_merge($chats1 , $chats2);

        if (!$chats)
            return $this -> apiResponse(null , 'There is no chat yet!' , 200);

        return $this -> apiResponse($chats , 'Chats return successfully !' , 200);
    }


    //get all the message in a chat
    public function chatMessages()
    {
        $chat_id = $_GET['id'];

        $chat = Chat::find($chat_id);
        if (!$chat)
        {
            return $this->apiResponse(null ,'chat not found!' , 404);
        }

        $messages = Message::query()
            -> where(['chat_id' => $chat_id])
            ->orderBy('created_at')
            -> get();

        if($messages->isEmpty())
        {
            return $this->apiResponse(null , 'chat is empty!' , 200);
        }

        return $this->apiResponse($messages , 'Done!' , 200);
    }


    //send a message
    public function sendMessage(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|between:1,100',
            'chat_id' => 'required|exists:chats,id',
        ]);

        $chat = Chat::find($request->chat_id);

        if ($chat->user_id != $user->id && $chat->second_user_id != $user->id)
        {
            return $this->apiResponse(null , 'you are not in this chat' , 400);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'chat_id' => $request->chat_id,
            'body' => $request->body
        ]);

        broadcast(new NewMessageSent($message))->toOthers();

        return $this->apiResponse($message , 'send successfully!' , 200);
    }
}
