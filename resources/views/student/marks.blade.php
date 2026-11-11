@extends('layouts.app')

@section('title', 'My Marks')

@section('content')
<h1 class="text-2xl font-bold mb-2">My Marks</h1>
<p class="text-slate-500 mb-6">Assessment results recorded for your courses.</p>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Course</th>
                    <th class="px-4 py-3 font-medium">Assessment</th>
                    <th class="px-4 py-3 font-medium">Mark</th>
                    <th class="px-4 py-3 font-medium">Recorded</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($marks as $mark)
                    <tr>
                        <td class="px-4 py-3">{{ $mark->course->course_code }} — {{ $mark->course->course_name }}</td>
                        <td class="px-4 py-3">{{ $mark->assessment_name }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $mark->score }} / {{ $mark->maximum_score }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $mark->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-5 text-slate-500">No marks have been recorded for you yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
