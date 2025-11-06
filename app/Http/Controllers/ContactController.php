<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormNotification;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ]);

        try {
            Log::info('=== CONTACT FORM START ===');
            Log::info('Form data:', $validated);

            // Save to database first
            $email = Email::create([
                'title' => $validated['subject'],
                'body' => $validated['message'],
                'sender_name' => $validated['name'],
                'sender_email' => $validated['email'],
                'recipient_email' => 'rohitgowtham796@gmail.com',
                'type' => 'contact_form',
                'is_sent' => false
            ]);

            Log::info('Database record created:', ['email_id' => $email->id]);

            // Test with simple email first
            Log::info('Attempting to send email...');
            
            // METHOD 1: Try simple email first
            Mail::raw('Simple test message from contact form', function($message) use ($validated) {
                $message->to('rohitgowtham796@gmail.com')
                        ->subject('Simple Test: ' . $validated['subject']);
            });
            
            Log::info('Simple email sent successfully');

            // METHOD 2: Now try with your mailable
            Log::info('Now trying with ContactFormNotification...');
            Mail::to('rohitgowtham796@gmail.com')->send(new ContactFormNotification($validated));
            
            Log::info('ContactFormNotification sent successfully');

            // Mark as sent
            $email->update([
                'is_sent' => true,
                'sent_at' => now()
            ]);

            Log::info('=== CONTACT FORM COMPLETED SUCCESSFULLY ===');

            return redirect()->route('contact')->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (\Exception $e) {
            Log::error('CONTACT FORM ERROR: ' . $e->getMessage());
            Log::error('Full trace: ', ['exception' => $e]);
            
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }
    
}
