<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalPayment;
use App\Models\Manuscript;
use App\Models\ManuscriptReview;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Driver-agnostic "format a date column as YYYY-MM" SQL fragment.
     * DATE_FORMAT() is MySQL-only; Postgres needs TO_CHAR(), SQLite
     * needs strftime(). Keeps monthly-trend queries portable.
     */
    protected function yearMonthExpr(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$column})",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Member dashboard — one page, sections toggle on/off per permission.
    |--------------------------------------------------------------------------
    |
    | A user can hold several Journal roles at once (e.g. Author +
    | Reviewer), so this is additive: every section whose permission
    | they carry gets built and shown, not just the "highest" one.
    |
    */
    public function index()
    {
        $user = Auth::user();

        $sections = [];

        if ($user->hasModulePermission('journal', 'submit-manuscript')) {
            $sections['author'] = $this->authorData($user);
        }

        if ($user->hasModulePermission('journal', 'review-manuscripts')) {
            $sections['reviewer'] = $this->reviewerData($user);
        }

        if ($user->hasModulePermission('journal', 'screen-submissions')) {
            $sections['associate_editor'] = $this->associateEditorData($user);
        }

        if ($user->hasModulePermission('journal', 'make-final-decision')) {
            $sections['editor_in_chief'] = $this->editorInChiefData($user);
        }

        // A Journal Manager (or Super Admin) doesn't carry any of the
        // pipeline permissions above — their whole job is the admin
        // dashboard. Send them straight there instead of showing them
        // an empty/misleading "Author" view.
        if (empty($sections) && ($user->isModuleAdmin('journal') || $user->isSuperAdmin())) {
            return redirect()->route('journal.admin.dashboard');
        }

        return view('modules.journal.dashboard', [
            'moduleLabel' => 'Journal Management',
            'sections' => $sections,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard — Journal Manager / Super Admin only.
    |--------------------------------------------------------------------------
    */
    public function admin()
    {
        $now = Carbon::now();
        $rangeStart = $now->copy()->subMonths(5)->startOfMonth();

        $totalManuscripts = Manuscript::count();
        $published = Manuscript::where('status', 'published')->count();
        $underReview = Manuscript::where('status', 'under_review')->count();
        $awaitingScreening = Manuscript::where('status', 'submitted')->count();
        $rejectedTotal = Manuscript::whereIn('status', ['desk_rejected', 'rejected'])->count();
        $awaitingDecision = Manuscript::where('status', 'under_review')
            ->whereNotNull('editor_decision_notes')
            ->whereNull('decided_at')
            ->count();

        $totalRevenue = (float) JournalPayment::where('status', 'completed')->sum('amount');
        $pendingPayments = JournalPayment::where('status', 'pending')->count();
        $waivedFees = JournalPayment::where('status', 'waived')->count();

        $journalRoleSlugs = Role::whereHas('module', fn ($q) => $q->where('code', 'journal'))
            ->pluck('id', 'slug');

        $totalAuthors = $journalRoleSlugs->has('author')
            ? User::whereHas('moduleRoles', fn ($q) => $q->where('roles.id', $journalRoleSlugs['author']))->count()
            : 0;

        $totalReviewers = $journalRoleSlugs->has('reviewer')
            ? User::whereHas('moduleRoles', fn ($q) => $q->where('roles.id', $journalRoleSlugs['reviewer']))->count()
            : 0;

        $avgReviewDays = (float) ManuscriptReview::where('status', 'submitted')
            ->whereNotNull('assigned_at')
            ->whereNotNull('submitted_at')
            ->get()
            ->avg(fn ($r) => $r->assigned_at->diffInDays($r->submitted_at));

        // Manuscripts by status (doughnut).
        $statusCounts = Manuscript::selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $chartStatus = [
            'labels' => collect(Manuscript::STATUSES)
                ->only($statusCounts->keys())
                ->values()
                ->all(),
            'data' => collect(Manuscript::STATUSES)
                ->keys()
                ->filter(fn ($k) => $statusCounts->has($k))
                ->map(fn ($k) => $statusCounts[$k])
                ->values()
                ->all(),
        ];

        // Manuscripts by category (bar).
        $categoryCounts = Manuscript::query()
            ->join('journal_categories', 'manuscripts.category_id', '=', 'journal_categories.id')
            ->selectRaw('journal_categories.name as name, count(*) as c')
            ->groupBy('journal_categories.name')
            ->orderByDesc('c')
            ->get();

        $chartCategory = [
            'labels' => $categoryCounts->pluck('name')->all(),
            'data' => $categoryCounts->pluck('c')->all(),
        ];

        // Submissions trend, last 6 months (line).
        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i)->format('Y-m'));

        $submissionsRaw = Manuscript::where('created_at', '>=', $rangeStart)
            ->selectRaw($this->yearMonthExpr('created_at') . ' as ym, count(*) as c')
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $chartSubmissions = [
            'labels' => $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->all(),
            'data' => $months->map(fn ($m) => (int) ($submissionsRaw[$m] ?? 0))->all(),
        ];

        // Revenue trend, last 6 months (bar).
        $revenueRaw = JournalPayment::where('status', 'completed')
            ->where('paid_at', '>=', $rangeStart)
            ->selectRaw($this->yearMonthExpr('paid_at') . ' as ym, sum(amount) as s')
            ->groupBy('ym')
            ->pluck('s', 'ym');

        $chartRevenue = [
            'labels' => $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->all(),
            'data' => $months->map(fn ($m) => (float) ($revenueRaw[$m] ?? 0))->all(),
        ];

        // Roles headcount (who holds what, in this module).
        $roles = Role::whereHas('module', fn ($q) => $q->where('code', 'journal'))
            ->withCount('users')
            ->orderByDesc('is_admin_role')
            ->get();

        $recentManuscripts = Manuscript::with(['author', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $overdueReviews = ManuscriptReview::with(['manuscript', 'reviewer'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $now)
            ->orderBy('due_date')
            ->take(8)
            ->get();

        $pendingPaymentsList = JournalPayment::with(['manuscript', 'author'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        return view('modules.journal.admin-dashboard', [
            'moduleLabel' => 'Journal Management',
            'stats' => [
                'total_manuscripts' => $totalManuscripts,
                'published' => $published,
                'under_review' => $underReview,
                'awaiting_screening' => $awaitingScreening,
                'awaiting_decision' => $awaitingDecision,
                'rejected_total' => $rejectedTotal,
                'total_revenue' => $totalRevenue,
                'pending_payments' => $pendingPayments,
                'waived_fees' => $waivedFees,
                'total_authors' => $totalAuthors,
                'total_reviewers' => $totalReviewers,
                'avg_review_days' => round($avgReviewDays ?? 0, 1),
            ],
            'chartStatus' => $chartStatus,
            'chartCategory' => $chartCategory,
            'chartSubmissions' => $chartSubmissions,
            'chartRevenue' => $chartRevenue,
            'roles' => $roles,
            'recentManuscripts' => $recentManuscripts,
            'overdueReviews' => $overdueReviews,
            'pendingPaymentsList' => $pendingPaymentsList,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Per-role section builders (member dashboard)
    |--------------------------------------------------------------------------
    */

    protected function authorData(User $user): array
    {
        $manuscripts = Manuscript::where('author_id', $user->id)->get();

        $statusCounts = $manuscripts->countBy('status');

        $chartStatus = [
            'labels' => collect(Manuscript::STATUSES)
                ->only($statusCounts->keys())
                ->values()
                ->all(),
            'data' => collect(Manuscript::STATUSES)
                ->keys()
                ->filter(fn ($k) => $statusCounts->has($k))
                ->map(fn ($k) => $statusCounts[$k])
                ->values()
                ->all(),
        ];

        $chartTrend = $this->monthlyTrend(
            Manuscript::where('author_id', $user->id),
            'created_at'
        );

        return [
            'total' => $manuscripts->count(),
            'published' => $manuscripts->where('status', 'published')->count(),
            'in_progress' => $manuscripts->whereIn('status', ['submitted', 'screening', 'under_review'])->count(),
            'needs_action' => $manuscripts->whereIn('status', ['revision_requested', 'desk_rejected'])->count()
                + $manuscripts->where('proof_status', 'sent')->count(),
            'awaiting_payment' => $manuscripts->where('status', 'accepted')
                ->filter(fn ($m) => ! $m->isFeeSettled())
                ->count(),
            'chart_status' => $chartStatus,
            'chart_trend' => $chartTrend,
            'recent' => $manuscripts->sortByDesc('created_at')->take(5)->values(),
        ];
    }

    protected function reviewerData(User $user): array
    {
        $reviews = ManuscriptReview::with('manuscript')
            ->where('reviewer_id', $user->id)
            ->get();

        $pending = $reviews->whereIn('status', ['assigned', 'in_progress']);
        $completed = $reviews->where('status', 'submitted');
        $declined = $reviews->where('status', 'declined');
        $overdue = $pending->filter(fn ($r) => $r->due_date && $r->due_date->isPast());

        $chartTrend = $this->monthlyTrend(
            ManuscriptReview::where('reviewer_id', $user->id)->where('status', 'submitted'),
            'submitted_at'
        );

        return [
            'total_assigned' => $reviews->count(),
            'pending' => $pending->count(),
            'completed' => $completed->count(),
            'overdue' => $overdue->count(),
            'chart_status' => [
                'labels' => ['Pending', 'Completed', 'Declined'],
                'data' => [$pending->count(), $completed->count(), $declined->count()],
            ],
            'chart_trend' => $chartTrend,
            'due_soon' => $pending->sortBy('due_date')->take(5)->values(),
        ];
    }

    protected function associateEditorData(User $user): array
    {
        $awaitingScreening = Manuscript::where('status', 'submitted')->count();

        $handledByMe = Manuscript::where('associate_editor_id', $user->id)->get();

        $pipelineCounts = $handledByMe->countBy('status');

        $chartTrend = $this->monthlyTrend(
            Manuscript::where('associate_editor_id', $user->id),
            'updated_at'
        );

        return [
            'awaiting_screening' => $awaitingScreening,
            'under_my_editorship' => $handledByMe->whereIn('status', ['screening', 'under_review', 'revision_requested'])->count(),
            'desk_rejected_by_me' => $handledByMe->where('status', 'desk_rejected')->count(),
            'total_handled' => $handledByMe->count(),
            'chart_pipeline' => [
                'labels' => collect(Manuscript::STATUSES)
                    ->only($pipelineCounts->keys())
                    ->values()
                    ->all(),
                'data' => collect(Manuscript::STATUSES)
                    ->keys()
                    ->filter(fn ($k) => $pipelineCounts->has($k))
                    ->map(fn ($k) => $pipelineCounts[$k])
                    ->values()
                    ->all(),
            ],
            'chart_trend' => $chartTrend,
            'queue' => Manuscript::with('author')
                ->where('status', 'submitted')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function editorInChiefData(User $user): array
    {
        $awaitingDecision = Manuscript::where('status', 'under_review')
            ->whereNotNull('editor_decision_notes')
            ->whereNull('decided_at')
            ->count();

        $underReview = Manuscript::where('status', 'under_review')->count();
        $decidedByMe = Manuscript::where('decided_by', $user->id)->count();
        $publishedTotal = Manuscript::where('status', 'published')->count();

        $decisionCounts = Manuscript::whereIn('status', ['accepted', 'rejected', 'revision_requested', 'published'])
            ->whereNotNull('decided_at')
            ->get()
            ->countBy('status');

        $chartTrend = $this->monthlyTrend(
            Manuscript::where('decided_by', $user->id)->whereNotNull('decided_at'),
            'decided_at'
        );

        return [
            'awaiting_decision' => $awaitingDecision,
            'under_review' => $underReview,
            'decided_by_me' => $decidedByMe,
            'published_total' => $publishedTotal,
            'chart_decisions' => [
                'labels' => collect(Manuscript::STATUSES)
                    ->only($decisionCounts->keys())
                    ->values()
                    ->all(),
                'data' => collect(Manuscript::STATUSES)
                    ->keys()
                    ->filter(fn ($k) => $decisionCounts->has($k))
                    ->map(fn ($k) => $decisionCounts[$k])
                    ->values()
                    ->all(),
            ],
            'chart_trend' => $chartTrend,
            'queue' => Manuscript::with('author')
                ->where('status', 'under_review')
                ->whereNotNull('editor_decision_notes')
                ->whereNull('decided_at')
                ->latest('updated_at')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Shared: monthly counts for the last 6 months (this month
     * included), against any query builder + date column. Returns
     * zero-filled labels/data ready to hand straight to a chart —
     * used for every "trend over time" line/bar on these dashboards.
     */
    protected function monthlyTrend($query, string $dateColumn): array
    {
        $now = Carbon::now();
        $rangeStart = $now->copy()->subMonths(5)->startOfMonth();

        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i)->format('Y-m'));

        $raw = (clone $query)
            ->where($dateColumn, '>=', $rangeStart)
            ->selectRaw($this->yearMonthExpr($dateColumn) . ' as ym, count(*) as c')
            ->groupBy('ym')
            ->pluck('c', 'ym');

        return [
            'labels' => $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->all(),
            'data' => $months->map(fn ($m) => (int) ($raw[$m] ?? 0))->all(),
        ];
    }
}
