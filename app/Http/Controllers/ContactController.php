<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function contact(): View
    {
        return view('contact');
    }

    public function contactlist(): View
    {
        return view('contactlist', [
            'contact' => Contact::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'campus_email' => ['required', 'email', 'max:255'],
            'topic' => ['required', 'in:Registration,Results,Fees,Other'],
            'message' => ['required', 'string', 'max:255'],
        ]);

        Contact::create($validated);

        return redirect()->route('contact')->with('status', 'Your message has been sent to the Registry.');
    }
}