<?php
namespace App\Http\Controllers\Mail;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class MailController extends Controller
{
    public function sendMail()
    {
        $details = [
            'name' => 'Delostyle Studio User'
        ];

        Mail::to('amarmaity243@gmail.com')->send(new TestMail($details));

        return "✅ Email sent successfully!";
    }
}
