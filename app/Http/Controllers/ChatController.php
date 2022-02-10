<?php

namespace App\Http\Controllers;

use App\Events\GroupCreatedEvent;
use App\Events\JoinChatChannelEvent;
use App\Events\MessageSentEvent;
use App\Group;
use App\Message;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{

    public function index()
    {
        $groups = Group::all();
        return view('chat.index', compact('groups'));
    }


    public function messages(Request $request)
    {
        //getting all the messages between the requested users.
        $from = $request->from;
        $to = $request->to;
        $groupID = $request->groupid;
        $myMessages = Message::where([ ['from', $from], ['to', $to] ])
            ->orWhere([ ['from', $to], ['to', $from] ])
            ->orWhere([['group_id', $groupID]])
            ->with('user')->get();

        foreach ($myMessages as $message) {
            $message->replyingToMessage = $message->reply_to_message_id ? Message::where('id', $message->reply_to_message_id)->with('user')->first() : null;
        }
        return $myMessages;
    }

    public function store(Request $request)
    {
        //storing a new message wheither it's a message, file or an image.
        // $currentDate = new DateTime();
        // dd($request->toUser);
        $replyToMessage = $request->replyToMessageID ? Message::where('id', $request->replyToMessageID)->with('user')->first() : null;
        if ($request->file('image')) {
            $file = $request->file('image');
            $fileExtension = $request->file('image')->getClientOriginalExtension();
            $fileType = null;
            $imageExtensions = ['jpg', 'png', 'jpeg'];
            $attachmentExtensions = ['pdf', 'docs', 'doc', 'docx'];
            if (in_array($fileExtension, $imageExtensions)) {
                $fileType = 'image';
            } else if (in_array($fileExtension, $attachmentExtensions)) {
                $fileType = 'file';
            } else {
                $fileExtension = 'wav';
                $fileType = 'audio';
            }
            $fileName = time() . '.' . $fileExtension;
            $file->move('chat', $fileName);
            $message = auth()->user()->messages()->create([
                'to' => $request->toUser !== 'null' && $request->toUser !== 'undefined' ? $request->toUser : NULL,
                'message' => $request->message,
                'file' => $fileName,
                'type' => $fileType,
                'group_id' => $request->groupID !== 'null' ? $request->groupID : NULL,
                'created_at' => $request->created_at,
                'reply_to_message_id' => $request->replyToMessageID ? $request->replyToMessageID : null,
                'forwarded' => $request->forwarded ? true : false

            ]);

            $message->replyingToMessage = $replyToMessage;
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        } else {
            $message = auth()->user()->messages()->create([
                'to' => $request->toUser !== 'null' && $request->toUser !== 'undefined' ? $request->toUser : NULL,
                'message' => $request->message,
                'file' => $request->file,
                'type' => $request->type,
                'created_at' => $request->created_at,
                'group_id' => $request->groupID !== 'null' ? $request->groupID : NULL,
                'reply_to_message_id' => $request->replyToMessageID ? $request->replyToMessageID : null,
                'forwarded' => $request->forwarded ? true : false
            ]);

            $message->replyingToMessage = $replyToMessage;
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        }
    }

    public function createGroup()
    {
        // broadcast(new JoinChatChannelEvent(auth()->user()))->toOthers();

        $users = User::all();
        return view('chat.createGroup', compact('users'));
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $group = Group::create([
            'name' => $request->name
        ]);

        $group->users()->attach($request->users);
        broadcast(new GroupCreatedEvent($group))->toOthers();
        Session::flash('success', 'Group Created Successfully!');
        return redirect()->route('chatGroup.index');
    }

    public function userRelatedUsersAndGroups(Request $request)
    {
        $authUser = User::find($request->user_id);
        $users = User::where('id', '!=', $authUser->id)
            ->where('id', '!=', auth()->user()->id)->get();
        $chatGroups = auth()->user()->groups()->get();

        return ['users' => $users, 'groups' => $chatGroups];
    }
}
