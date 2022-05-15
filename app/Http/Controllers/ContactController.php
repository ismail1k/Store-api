<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Auth;

class ContactController extends Controller
{
    private static function clientIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }

    public function all(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'contact_all')){
                $response = [];
                foreach(Contact::all() as $mail){
                    array_push($response, [
                        'id' => $mail->id,
                        'firstname' => $mail->firstname,
                        'lastname' => $mail->lastname,
                        'email' => $mail->email,
                        'read' => $mail->read,
                        'subject' => $mail->subject,
                        'created_at' => $mail->created_at,
                    ]);
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function view(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'contact_view')){
                if($mail = Contact::whereId($request['mail_id'])->first()){
                    Contact::whereId($request['mail_id'])->update(['read' => true]);
                    $response = [
                        'id' => $mail->id,
                        'firstname' => $mail->firstname,
                        'lastname' => $mail->lastname,
                        'email' => $mail->email,
                        'subject' => $mail->subject,
                        'message' => $mail->message,
                        'created_by' => $mail->created_by,
                        'created_at' => $mail->created_at->toDayDateTimeString(),
                    ];
                    return response()->json($response);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function send(Request $request){
        $fname = lib::filter($request['firstname']);
        $lname = lib::filter($request['lastname']);
        $email = lib::filter($request['email']);
        $subject = lib::filter($request['subject']);
        $message = lib::filter($request['message']);
        $ip = self::clientIp();
        $created_by = Auth::check()?Auth::user()->id:null;
        $created = Contact::create([
            'firstname' => $fname,
            'lastname' => $lname,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip' => $ip,
            'created_by' => $created_by,
        ]);
        if($created){
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 500]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'contact_remove')){
                if(Contact::whereId($request['mail_id'])->delete()){
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
