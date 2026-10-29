@extends('layouts.main')
@section('content')
    
    <!-- Start header -->
@include('layouts.header')
    <!-- End header -->

    <!-- Start main -->
    <main class="pt-32 pb-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-2">Contact the Registry</h1>
        <p class="text-sm text-slate-500 mb-8">Questions about records, results or fees? Send a message and we will reply within two working days.</p>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="space-y-4 text-sm">
                <div class="flex gap-3"><i data-lucide="map-pin" class="w-5 h-5 text-[#2563eb] shrink-0"></i><span>Registry Office, Main Administration Block</span></div>
                <div class="flex gap-3"><i data-lucide="mail" class="w-5 h-5 text-[#2563eb] shrink-0"></i><span>registry@campus.ac.ug</span></div>
                <div class="flex gap-3"><i data-lucide="clock" class="w-5 h-5 text-[#d97706] shrink-0"></i><span>Mon–Fri, 8:00am – 5:00pm</span></div>
            </div>
            <form id="contactForm"  class="md:col-span-2 rounded-2xl border border-slate-200 p-6 space-y-4" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><label for="name" class="block text-sm font-medium text-slate-600 mb-2">Name</label><input id="name" name="full_name" required class="input-field"></div>
                    <div><label for="email" class="block text-sm font-medium text-slate-600 mb-2">Email</label><input id="email" type="email" name="campus_email" required class="input-field"></div>
                </div>
                <div><label for="topic" class="block text-sm font-medium text-slate-600 mb-2">Topic</label><select id="topic" class="input-field" name="topic">
                    <option>Registration</option>
                    <option>Results</option>
                    <option>Fees</option>
                    <option>Other</option>
                </select></div>
                <div><label for="message" class="block text-sm font-medium text-slate-600 mb-2">Message</label><textarea id="message" rows="5" required class="input-field" name="message"></textarea></div>
                <button class="sm:w-auto py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Send message</button>
            </form>
        </div>
    </main>
    <!-- End main -->

    <!-- Start footer -->
@include('layouts.footer')
    <!-- End footer -->
    
@endsection
<!-- End html -->
