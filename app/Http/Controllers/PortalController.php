<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Book;
use App\Models\Manuscript;
use App\Models\Module;
use App\Models\RepositoryItem;
use Illuminate\Support\Facades\Route;

/**
 * The public front door of the platform. Unlike /dashboard (which is
 * only reachable once logged in), this page is open to everyone and
 * gives every visitor — member or not — one place to see every
 * active module and get to whichever public area or sign-up flow
 * applies to it.
 */
class PortalController extends Controller
{
    public function index()
    {
        $order = ['journal', 'ebook', 'library', 'researcher', 'wiki', 'repository'];

        $modules = Module::active()
            ->get()
            ->sortBy(fn (Module $module) => array_search($module->code, $order) ?? 99)
            ->values();

        $cards = $modules->map(fn (Module $module) => $this->buildCard($module))->all();

        return view('portal.index', compact('cards'));
    }

    /**
     * One card per module: what it is, a live headline count when the
     * module publishes public content, and the single best link for
     * a visitor — browse if the module has a public portal, join if
     * it's open to self-registration, otherwise sign in.
     */
    private function buildCard(Module $module): array
    {
        [$blurb, $total, $totalLabel] = match ($module->code) {
            'journal' => [
                'Peer-reviewed research from and about the Oromo community — browse published articles or submit your own manuscript.',
                Manuscript::published()->count(),
                'published articles',
            ],
            'ebook' => [
                'A digital library of books by Oromo authors, from open-access reads to titles available on loan.',
                Book::published()->count(),
                'published titles',
            ],
            'library' => [
                'The Association\'s physical and digital collection — catalog, loans, and holds for registered members.',
                null,
                null,
            ],
            'researcher' => [
                'A network for researchers to build a profile, form groups, and connect with peers working on related topics.',
                null,
                null,
            ],
            'wiki' => [
                'A community-written encyclopedia of Oromo history, language, and culture, free for anyone to read.',
                Article::published()->count(),
                'published articles',
            ],
            'repository' => [
                'An open scholarly archive of theses, datasets, and reports — discoverable and citable by anyone.',
                RepositoryItem::published()->count(),
                'published items',
            ],
            default => [$module->description, null, null],
        };

        return [
            'code' => $module->code,
            'name' => $module->name,
            'icon' => $module->icon ?: 'bi-grid',
            'blurb' => $blurb,
            'total' => $total,
            'total_label' => $totalLabel,
            'cta' => $this->cta($module),
        ];
    }

    /**
     * Where the "primary" button on a module's card should point:
     * a public browse page if one is routed, otherwise a self-
     * registration page if the module allows it, otherwise sign in.
     */
    private function cta(Module $module): array
    {
        if (Route::has("{$module->code}.public.index")) {
            return [
                'label' => 'Browse',
                'url' => route("{$module->code}.public.index"),
            ];
        }

        if ($module->code === 'researcher' && Route::has('researcher.register')) {
            return [
                'label' => 'Join the network',
                'url' => route('researcher.register'),
            ];
        }

        if ($module->is_self_registerable && Route::has('register')) {
            return [
                'label' => 'Sign up',
                'url' => route('register'),
            ];
        }

        return [
            'label' => 'Member sign in',
            'url' => route('login'),
        ];
    }
}
