<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Messages;
use App\Contacts;

class MessagesController extends Controller
{
    public function contacts(Request $request) {
        $to = Contacts::whereNull('deleted_at')->where('from', $request->id)->pluck('to');
        $from = Contacts::whereNull('deleted_at')->where('to', $request->id)->pluck('from');
        $ID = array_merge(collect($to)->toArray(),collect($from)->toArray());

        $users = User::where('id', '!=', $request->id)->whereIn('id', $ID)->get();

        $arr = [];
        foreach($users as $user) {
            $messages = Messages::whereNull('deleted_at')->where('from', $user->id)->where('to', $request->id)->where('read', 0)->get();
            $arr[] = collect($user)->merge(['unread' => count($messages)]);
        }
        return response()->json($arr);
    }
    public function getMessagesFor(Request $request, $id) {
        Messages::whereNull('deleted_at')->where('from', $id)->where('to', $request->id)->update(['read' => true]);

        $authID = $request->id;
        $messages = Messages::whereNull('deleted_at')->where(function($q) use ($id, $authID) {
            $q->where('from', $authID);
            $q->where('to', $id);
        })->orWhere(function($q) use ($id, $authID) {
            $q->where('from', $id);
            $q->where('to', $authID );
        })
        ->get();

        return response()->json($messages);
    }
    public function send(Request $request) {
        $message = Messages::create([
            'from' => $request->id,
            'to' => $request->contact_id,
            'text' => $request->text
        ]);

        return response()->json($message);
    }
    public function addContact(Request $request) {
        if(isset($request->user_id) && isset($request->id)) {
            $add = new Contacts;
            $add->from = $request->user_id;
            $add->to = $request->id;
            $add->save();
            return response()->json(['success' => true], 200);   
        } else {
            return response()->json(['failed' => true], 404);
        }
    }
}
