<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Messages;
use App\Contacts;

class MessagesController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function contacts(Request $request) {
        // return response()->json(Auth::user());  
        $to = Contacts::whereNull('deleted_at')->where('from', Auth::user()->id)->pluck('to');
        $from = Contacts::whereNull('deleted_at')->where('to', Auth::user()->id)->pluck('from');
        $ID = array_merge(collect($to)->toArray(),collect($from)->toArray());

        $users = User::where('id', '!=', $request->id)->whereIn('id', $ID)->get();

        $arr = [];
        foreach($users as $user) {
            $messages = Messages::whereNull('deleted_at')->where('from', $user->id)->where('to', Auth::user()->id)->where('read', 0)->get();
            $arr[] = collect($user)->merge(['unread' => count($messages)]);
        }
        return response()->json($arr);
    }
    public function getMessagesFor(Request $request, $id) {
        Messages::whereNull('deleted_at')->where('from', $id)->where('to', Auth::user()->id)->update(['read' => true]);

        $authID = Auth::user()->id;
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
        $user = Auth::user();

        $message = Messages::create([
            'from' => Auth::user()->id,
            'to' => $request->contact_id,
            'text' => $request->text
        ]);
        return response()->json($message);
    }
    public function addContact(Request $request) {
        if(isset($request->id)) {
            $contact = Contacts::whereNull('deleted_at')->where('from', Auth::user()->id)->where('to', $request->id)->first();
            if(!isset($contact)) {
                $add = new Contacts;
                $add->from = Auth::user()->id;
                $add->to = $request->id;
                $add->save();
            }
            return response()->json(['success' => true], 200);   
        } else {
            return response()->json(['failed' => true], 404);
        }
    }
}
