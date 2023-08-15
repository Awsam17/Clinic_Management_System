<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{

    use ApiResponseTrait;

    //create and check chat



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

//        if (!$chats)
//            return $this -> apiResponse(null , 'There is no chat yet!' , 200);

        return $this -> apiResponse($chats , 'Chats return successfully !' , 200);
    }


    //get all the message in a chat



    //send a message
}
