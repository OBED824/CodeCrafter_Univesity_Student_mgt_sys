<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);
        [$startDate, $endDate] = $this->dateRange($filters);

        return view('admin.reports.index', [
            'attendanceSummary' => $this->attendanceSummary($startDate, $endDate, $filters['course_id'] ?? null),
            'marksSummary' => $this->marksSummary($startDate, $endDate, $filters['course_id'] ?? null),
            'courses' => Course::orderBy('course_code')->get(),
            'filters' => [
                ...$filters,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'studentCounts' => [
                'active' => Student::where('status', 'active')->count(),
                'transferred' => Student::where('status', 'transferred')->count(),
                'graduated' => Student::where('status', 'graduated')->count(),
                'withdrawn' => Student::where('status', 'withdrawn')->count(),
            ],
            'accountCounts' => [
                'administrators' => User::role('administrator')->count(),
                'lecturers' => User::role('lecturer')->count(),
                'students' => User::role('student')->count(),
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);
        [$startDate, $endDate] = $this->dateRange($filters);
        $courseId = $filters['course_id'] ?? null;
        $attendanceSummary = $this->attendanceSummary($startDate, $endDate, $courseId);
        $marksSummary = $this->marksSummary($startDate, $endDate, $courseId);

        return response()->streamDownload(function () use ($attendanceSummary, $marksSummary, $startDate, $endDate): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Institutional academic report', $startDate->toDateString(), $endDate->toDateString()]);
            fputcsv($output, []);
            fputcsv($output, ['Attendance summary', 'Course code', 'Course name', 'Attendance records']);

            foreach ($attendanceSummary as $summary) {
                fputcsv($output, [
                    'Attendance',
                    $this->spreadsheetSafe($summary->course->course_code),
                    $this->spreadsheetSafe($summary->course->course_name),
                    $summary->attendance_count,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['Marks summary', 'Course code', 'Course name', 'Assessments', 'Average percentage']);

            foreach ($marksSummary as $summary) {
                fputcsv($output, [
                    'Marks',
                    $this->spreadsheetSafe($summary->course->course_code),
                    $this->spreadsheetSafe($summary->course->course_name),
                    $summary->assessment_count,
                    round((float) $summary->average_percentage, 2),
                ]);
            }

            fclose($output);
        }, 'institutional-report.csv', ['Content-Type' => 'text/csv']);
    }

    private function attendanceSummary(Carbon $startDate, Carbon $endDate, ?int $courseId): Collection
    {
        return Attendance::query()
            ->select('course_id')
            ->selectRaw('COUNT(*) as attendance_count')
            ->whereBetween('recorded_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
            ->groupBy('course_id')
            ->with('course:id,course_code,course_name')
            ->orderBy('course_id')
            ->get();
    }

    private function marksSummary(Carbon $startDate, Carbon $endDate, ?int $courseId): Collection
    {
        return Mark::query()
            ->select('course_id')
            ->selectRaw('COUNT(*) as assessment_count')
            ->selectRaw('SUM(score) * 100.0 / NULLIF(SUM(maximum_score), 0) as average_percentage')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
            ->groupBy('course_id')
            ->with('course:id,course_code,course_name')
            ->orderBy('course_id')
            ->get();
    }

    /**
     * @param  array{start_date?: string, end_date?: string, course_id?: int}  $filters
     * @return array{Carbon, Carbon}
     */
    private function dateRange(array $filters): array
    {
        $startDate = isset($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->startOfMonth();
        $endDate = isset($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->startOfDay()
            : now()->endOfDay();

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'The end date must be on or after the start date.',
            ]);
        }

        return [$startDate, $endDate];
    }

    private function spreadsheetSafe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) === 1 ? "'".$value : $value;
    }
}
