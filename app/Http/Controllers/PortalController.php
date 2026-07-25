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

        return view('portal.index', [
            'cards' => $cards,
            'heroSlides' => $this->heroSlides(),
            'team' => $this->team(),
            'testimonials' => $this->testimonials(),
            'roadmap' => $this->roadmap(),
            'joinModules' => $modules->map(fn (Module $m) => ['id' => $m->id, 'name' => $m->name])->values()->all(),
        ]);
    }

    /**
     * Hero slideshow content. Each slide expects an image at
     * public/assets/img/hero/{image}. Ship your own photos under
     * those filenames (or edit the paths below) — the overlay
     * gradient in the view keeps text readable over any image.
     */
    private function heroSlides(): array
    {
        return [
            [
                'image' => 'hero/hero-1.jpg',
                'eyebrow' => 'Oromo Research Association',
                'title' => 'Preserving knowledge, building the future.',
                'subtitle' => 'One platform for Oromo scholarship, publishing, and community.',
            ],
            [
                'image' => 'hero/hero-2.jpg',
                'eyebrow' => 'Publish & discover',
                'title' => 'Research that reaches its community.',
                'subtitle' => 'Peer-reviewed journals, open books, and an archive anyone can cite.',
            ],
            [
                'image' => 'hero/hero-3.jpg',
                'eyebrow' => 'Connect & contribute',
                'title' => 'A network built by and for Oromo researchers.',
                'subtitle' => 'Find collaborators, join a group, and write for Oromo Wikipedia.',
            ],
        ];
    }

    /**
     * Placeholder team roster — replace with real names, roles, and
     * photos (or wire this up to a database table later if the team
     * changes often enough to need an admin screen).
     */
    private function team(): array
    {
        return [
            ['name' => 'Executive Director', 'role' => 'Strategy & Partnerships', 'photo' => null],
            ['name' => 'Research Lead', 'role' => 'Journal & Peer Review', 'photo' => null],
            ['name' => 'Community Lead', 'role' => 'Researcher Network & Wiki', 'photo' => null],
            ['name' => 'Platform Lead', 'role' => 'Library & Repository', 'photo' => null],
        ];
    }

    /**
     * Placeholder testimonials — swap in real quotes (with consent)
     * as they come in.
     */
    private function testimonials(): array
    {
        return [
            ['quote' => 'Publishing through the Journal module made the review process transparent from submission to decision.', 'name' => 'Journal Author'],
            ['quote' => 'The Researcher Network helped me find collaborators working on the same questions I was.', 'name' => 'Network Member'],
            ['quote' => 'Having the eBook library and Repository in one place makes citing sources far easier.', 'name' => 'Repository User'],
        ];
    }

    /**
     * Placeholder roadmap — update the labels/status as milestones
     * actually land.
     */
    private function roadmap(): array
    {
        return [
            ['label' => 'Platform launch', 'detail' => 'Journal, Ebook, Library, Researcher Network, Wiki, and Repository modules live.', 'status' => 'done'],
            ['label' => 'Public portal', 'detail' => 'One landing page for every module, plus Join and Contact forms.', 'status' => 'done'],
            ['label' => 'Mobile app', 'detail' => 'A companion app for reading and notifications on the go.', 'status' => 'planned'],
            ['label' => 'Multilingual interface', 'detail' => 'Afaan Oromoo alongside English across every module.', 'status' => 'planned'],
        ];
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
