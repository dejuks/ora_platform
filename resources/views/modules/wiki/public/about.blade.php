@extends('components.wiki')

@section('title', 'About Oromo Wikipedia — Oromo Wikipedia')
@section('tab-about', 'active')

@section('content')

    <h1 class="ow-page-title ow-serif">About Oromo Wikipedia</h1>
    <p class="ow-page-sub">From Oromo Wikipedia, the free encyclopedia</p>

    <div class="ow-content-grid">
        <div class="ow-results-col">
            <div class="ow-article-body">

                <p>
                    Oromo Wikipedia is a free, publicly readable encyclopedia written in Afaan Oromoo.
                    It is built and maintained as part of the wider Oromo Research Association (ORA)
                    platform, alongside the Journal, Ebook, Library, and Researcher Network modules.
                </p>

                <h2 class="ow-serif">What Oromo Wikipedia is</h2>
                <p>
                    Oromo Wikipedia collects articles on history, culture, language, geography, science,
                    and public life relevant to the Oromo people and Oromia. Every article is written to
                    be read by anyone, without needing an account or subscription.
                </p>
                <p>
                    Reading is always free and open. Writing and editing require a registered account so
                    that changes can be reviewed and attributed to a contributor.
                </p>

                <h2 class="ow-serif">Our mission</h2>
                <p>
                    Much of Oromo history, language, and scholarship exists in scattered sources,
                    oral tradition, or academic archives that are hard for the public to reach. Oromo
                    Wikipedia's mission is to gather that knowledge into one place, written in Afaan
                    Oromoo, and keep it freely available to anyone who wants to learn.
                </p>

                <h2 class="ow-serif">How it works</h2>
                <p>
                    Registered contributors submit and edit articles. Depending on an article's
                    protection level, changes may go through a review step before they are published,
                    which is why some pages show a lock icon and a protection label next to the last
                    updated date.
                </p>
                <p>
                    Articles are organized into categories to make related topics easy to find, and can
                    be searched from the search bar at the top of every page.
                </p>

                <h2 class="ow-serif" id="contributor-guidelines">Contributor Guidelines</h2>
                <p>
                    Anyone with an account can create or edit an article. A few ground rules keep the
                    encyclopedia trustworthy:
                </p>
                <ul>
                    <li>Write neutrally — describe topics fairly rather than arguing a point of view.</li>
                    <li>Cite sources where a claim can reasonably be checked.</li>
                    <li>Write original text; don't copy from other publications.</li>
                    <li>Use the edit summary field to briefly explain what changed and why.</li>
                    <li>Protected or disputed pages route through review before changes go live.</li>
                    <li>If you think a page should be removed, open a deletion discussion rather than
                        editing it out — the community weighs in before an Administrator closes it.</li>
                </ul>

                <h2 class="ow-serif">Editorial approach</h2>
                <p>
                    Contributors are expected to write neutrally, cite sources where claims can be
                    checked, and avoid copying text from other publications. Disputed or sensitive
                    topics may carry a higher protection level, meaning edits are reviewed before they
                    go live rather than published immediately.
                </p>

                <h2 class="ow-serif">Licensing</h2>
                <p>
                    Article text on Oromo Wikipedia is made available for public reading. Reuse
                    elsewhere should credit Oromo Wikipedia as the source. Specific licensing terms for
                    reuse and redistribution will be published here as the platform matures.
                </p>

                <h2 class="ow-serif">Get involved</h2>
                <p>
                    You don't need any special expertise to help. Fixing a typo, expanding a short
                    article, or adding a missing topic are all useful contributions.
                </p>
                <p>
                    <a href="{{ route('login') }}">Sign in</a> to start editing, or use the search bar
                    above to find an article that could use more detail.
                </p>

            </div>
        </div>

        <div class="ow-aside-col">
            <div class="ow-notice">
                <strong>Oromo Wikipedia</strong> is part of the Oromo Research Association (ORA)
                platform, alongside Journal, Ebook, Library, and Researcher Network modules.
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Quick facts</div>
                <div class="ow-box-body">
                    <ul>
                        <li>Free to read, no account required</li>
                        <li>Written in Afaan Oromoo</li>
                        <li>Edits reviewed before publishing on protected pages</li>
                    </ul>
                </div>
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Related pages</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="{{ route('wiki.public.index') }}">Browse all articles</a></li>
                        <li><a href="{{ route('login') }}">Sign in to contribute</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
