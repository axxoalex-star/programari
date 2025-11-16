<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class EmailController extends Controller
{
    public function sendTestEmail(Request $request)
    {
        try {
            $emailAddress = $request->input('email', 'admin@testaxxo.site');

            Mail::to($emailAddress)->send(new TestEmail());

            return response()->json([
                'success' => true,
                'message' => 'Email trimis cu succes către ' . $emailAddress
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Eroare la trimiterea email-ului: ' . $e->getMessage()
            ], 500);
        }
    }
}
