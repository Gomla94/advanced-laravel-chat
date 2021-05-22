<?php

namespace App\Http\Controllers;

use App\Events\GroupCreatedEvent;
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
        $myMessages = Message::where([['from', $from], ['to', $to]])
            ->orWhere([['from', $to], ['to', $from]])
            ->orWhere([['group_id', $groupID]])
            ->with('user')->get();
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
                'to' => $request->toUser == 'undefined' ? null : $request->toUser,
                'message' => $request->message,
                'file' => $fileName,
                'type' => $fileType,
                'group_id' => $request->groupID == 'undefined' ? null : $request->groupID,
                'created_at' => $currentDate
            ]);
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        } else {
            $message = auth()->user()->messages()->create([
                'to' => $request->toUser == 'undefined' ? null : $request->toUser,
                'message' => $request->message,
                'created_at' => $currentDate,
                'group_id' => $request->groupID == 'undefined' ? null : $request->groupID,
            ]);
            broadcast(new MessageSentEvent($message->load('user'), $request->fromUser, $request->toUser))->toOthers();
            return ['status' => 200, 'message' => $message];
        }
    }

    public function createGroup()
    {
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
}
