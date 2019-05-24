<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;


class MailController extends Controller
{
    public function mail(Request $request){

        $data = array(
            'email'=>'testemailpwe@mailinator.com',
            'password' => 'tests'
        );
        $template_path = 'emails/createUser';

        // Mail::send(['text'=>$template_path], $data, function($message){
        //     $message->to('testemailpwe@mailinator.com', 'Test Email PWE')->subject('Testing Email Service');
        //     $message->from('portal@paymentworld.eu','PaymentWorld EU Portal');

        // });

        // try {
            
        // } catch ($error) {
        //     $response = [
        //         "status" => 1,
        //         "message" => "Email Not Sent",
        //         "data" => $error
        //     ];
        //     return response()->json($response, 401);
        // }

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send($template_path, $data, function($message)
        {
            $message
                ->from('portal@paymentworld.eu','PaymentWorld EU Portal')
                ->to('testemailpwe@mailinator.com', 'User Created')
                ->subject('A user has been created');
        });
        

        $response = [
            "status" => 0,
            "message" => "Email Sent"
        ];

        return response()->json($response, 200);
    }
}
