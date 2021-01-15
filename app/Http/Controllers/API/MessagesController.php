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
        $contacts_id = Contacts::whereNull('deleted_at')->where('from', $request->id)->pluck('to');
        $contacts = User::where('id', '!=', $request->id)->whereIn('id', $contacts_id)->get();

        $arr = [];
        foreach($contacts as $contact) {
            $messages = Messages::whereNull('deleted_at')->where('from', $contact->id)->where('to', $request->id)->where('read', 0)->get();
            $arr[] = collect($contact)->merge(['unread' => count($messages)]);
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
}
