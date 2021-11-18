<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Events\MessageReceived;
use App\Models\AllowedChat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
    * Get the messages b/w currently logged-in user and the user id passed through parameter
     */
    public function chatWithUser( $secondUserId)
    {
        $loggedInUser = auth()->id();
        $messages = Message::chatBetweenUsers($loggedInUser, $secondUserId);

        // reverse the array to get the latest messages at the end
        $messages = array_reverse($messages);
        $response = [
            'chat' => $messages,
        ];

        return response($response, 200);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
           'message_text' => 'required',
            'to' => 'required|numeric|min:1'
        ]);

        $messageText = $request->message_text;
        $sendTo = User::findOrFail($request->to);

        $message = new Message();
        $message->message_text = $messageText;
        $message->message_type = MessageType::Text;     //TODO: Will change latter
        //$message->to()->associate($sendTo);
        //$message->from()->associate(auth()->user());

        $message->to = $sendTo->id;
        $message->from = auth()->user()->id;
        if($message->save()){
            // Broadcast the message to the receiving user
            broadcast(new MessageReceived($message, $sendTo->id));
            $response = [
                'status' => 'Message Sent!',
                'message' => $message
            ];
            return response($response, 200);

        } else{
            $response = ['status' => 'Message Sending Failed!'];
            return response($response, 403);
        }
    }

    /**
    * Add a user in allowed chats of the currently logged-in user
     */
    public function addInChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1'
        ]);

        $userToAdd = User::findOrFail($request->user_id);
        $loggedInUser = auth()->user();
        if(!array_key_exists($userToAdd->id , $loggedInUser->allowedChats())){
            $allowedChat = new AllowedChat();
            $allowedChat->user1()->associate($loggedInUser);
            $allowedChat->user2()->associate($userToAdd);

            if($allowedChat->save()){
                $response = [
                    'status' => 'User added successfully'
                ];
                return response($response, 200);
            }

        }

        return response(['status' => 'User was already added'], 200);
    }
}
