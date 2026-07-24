<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Book;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\Manuscript;
use App\Models\Module;
use App\Models\RepositoryItem;
use App\Models\ResearchGroup;
use App\Models\ResearchGroupMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    /**
     * Smart landing route. Every logged-in user hits /dashboard; this
     * is the single place that decides where they actually belong.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $moduleCode = $user->moduleRoles()
            ->with('module')
            ->get()
            ->pluck('module.code')
            ->unique()
            ->sort()
            ->first();

        if ($moduleCode) {
            return redirect()->route("{$moduleCode}.dashboard");
        }

        return view('dashboard.no-access');
    }

    /**
     * Super Admin control-panel dashboard — a real, live rollup across
     * every module in the system, not per-module detail (each module
     * has its own admin dashboard for that).
     */
    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'Active')->count(),
            'inactive_users' => User::where('status', '!=', 'Active')->count(),
            'total_modules' => Module::count(),
            'active_modules' => Module::where('is_active', true)->count(),
            'super_admins' => User::where('is_super_admin', true)->count(),
        ];

        $moduleCards = $this->buildModuleCards();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'moduleCards' => $moduleCards,
            'recentUsers' => $recentUsers,
            'userGrowth' => $this->userGrowthSeries(),
            'contentByModule' => $this->contentByModuleSeries($moduleCards),
            'usersByModule' => $this->usersByModuleSeries($moduleCards),
        ]);
    }

    /**
     * One summary card per module: headline content count, a secondary
     * status count, distinct member count, and where to send an admin
     * who wants the module's own dashboard.
     */
    private function buildModuleCards(): array
    {
        // SQLite (this app's default) has no FIELD()/ORDER BY CASE
        // shorthand worth reaching for here, so sort in PHP against a
        // fixed display order that mirrors the module rollout.
        $order = ['journal', 'ebook', 'library', 'researcher', 'wiki', 'repository'];
        $modules = Module::all()->sortBy(fn ($m) => array_search($m->code, $order) ?? 99)->values();

        return $modules->map(function (Module $module) {
            [$total, $totalLabel, $secondary, $secondaryLabel] = match ($module->code) {
                'journal' => [
                    Manuscript::count(),
                    'Manuscripts',
                    Manuscript::published()->count(),
                    'Published',
                ],
                'ebook' => [
                    Book::count(),
                    'Books',
                    Book::where('status', 'published')->count(),
                    'Published',
                ],
                'library' => [
                    LibraryBook::count(),
                    'Titles',
                    LibraryLoan::where('status', 'active')->count(),
                    'Active loans',
                ],
                'researcher' => [
                    ResearchGroup::count(),
                    'Groups',
                    ResearchGroupMember::where('status', 'pending')->count(),
                    'Pending requests',
                ],
                'wiki' => [
                    Article::count(),
                    'Articles',
                    Article::published()->count(),
                    'Published',
                ],
                'repository' => [
                    RepositoryItem::count(),
                    'Items',
                    RepositoryItem::published()->count(),
                    'Published',
                ],
                default => [0, 'Items', 0, ''],
            };

            return [
                'code' => $module->code,
                'name' => $module->name,
                'icon' => $module->icon ?: 'bi-grid',
                'is_active' => (bool) $module->is_active,
                'members' => $module->userCount(),
                'total' => $total,
                'total_label' => $totalLabel,
                'secondary' => $secondary,
                'secondary_label' => $secondaryLabel,
                'route' => Route::has("{$module->code}.admin.dashboard")
                    ? "{$module->code}.admin.dashboard"
                    : null,
            ];
        })->all();
    }

    /**
     * New-user registrations for each of the last 12 months, oldest
     * first — feeds the growth line chart.
     */
    private function userGrowthSeries(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->startOfMonth()->subMonths($i);

            $labels[] = $month->format('M');
            $counts[] = User::whereBetween('created_at', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ])->count();
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    /**
     * Total content volume per module — feeds the bar chart comparing
     * how much has been produced in each module.
     */
    private function contentByModuleSeries(array $moduleCards): array
    {
        return [
            'labels' => array_column($moduleCards, 'name'),
            'data' => array_column($moduleCards, 'total'),
        ];
    }

    /**
     * Distinct member count per module — feeds the donut chart showing
     * where the platform's members are concentrated.
     */
    private function usersByModuleSeries(array $moduleCards): array
    {
        return [
            'labels' => array_column($moduleCards, 'name'),
            'data' => array_column($moduleCards, 'members'),
        ];
    }
}
