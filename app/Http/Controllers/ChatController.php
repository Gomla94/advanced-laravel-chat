<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Message;
use DateTime;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        //getting all the messages between the requested users.
        $from = $request->from;
        $to = $request->to;
        $myMessages = Message::where([['from', $from], ['to', $to]])
            ->orWhere([['from', $to], ['to', $from]])->with('user')->get();
        return $myMessages;
    }

    public function store(Request $request)
    {
        //storing a new message wheither it's a message, file or an image.
        $currentDate = new DateTime();
        if ($request->image) {
            $file = $request->file('image');
            $fileExtension = $request->file('image')->getClientOriginalExtension();
            $fileType = null;
            $imageExtensions = ['jpg', 'png', 'jpeg'];
            if (in_array($fileExtension, $imageExtensions)) {
                $fileType = 'image';
            } else {
                $fileType = 'file';
            }
            $fileName = time() . '.' . $fileExtension;
            $file->move('chat', $fileName);
            // $path = $file->move('chat', $fileName);
            $message = auth()->user()->messages()->create([
                'to' => $request->toUser,
                'message' => $request->message,
                'file' => $fileName,
                'type' => $fileType,
                'created_at' => $currentDate
            ]);
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        } else {
            $message = auth()->user()->messages()->create([
                'to' => $request->toUser,
                'message' => $request->message,
                'created_at' => $currentDate
            ]);
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        }
    }
}
