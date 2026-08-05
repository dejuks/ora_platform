# Journal Management Dashboard — Drop-in Files (v2)

Replace these files in your `ora_platform` repo (same paths):

- `app/Http/Controllers/Journal/DashboardController.php`
- `resources/views/modules/journal/dashboard.blade.php`
- `resources/views/modules/journal/admin-dashboard.blade.php`

## What's fixed since v1

**Bug: Journal Manager landed on the "As an Author" member dashboard.**
The Journal Manager role doesn't carry any of the four pipeline
permissions (`submit-manuscript`, `review-manuscripts`,
`screen-submissions`, `make-final-decision`) — that's by design, a
Journal Manager administers the module rather than participating in
the manuscript pipeline. The old code's fallback silently defaulted an
empty dashboard to the Author view, which is what you saw. Fixed:
`index()` now redirects a module admin (Journal Manager) or Super Admin
straight to the real admin dashboard (`journal.admin.dashboard`) when
they have none of those four permissions. Everyone else still sees
whichever role sections actually apply to them.

**More chart variety, not just donuts.** Every role section on the
member dashboard now has two charts side by side instead of one:

- **Author**: donut (manuscripts by status) + **line chart** (my
  submissions, last 6 months)
- **Reviewer**: donut (pending/completed/declined) + **column bar
  chart** (reviews completed, last 6 months)
- **Associate Editor**: horizontal bar (pipeline by status) + **area
  chart** (my screening activity, last 6 months)
- **Editor-in-Chief**: horizontal bar (final decisions — was a donut,
  changed for variety) + **line chart** (my decisions, last 6 months)

The admin dashboard already mixed donut, area, and bar charts and is
unchanged from v1.

## Notes / assumptions
- "Awaiting EIC decision" = a manuscript still `under_review` where an
  Associate Editor has written `editor_decision_notes` (their
  recommendation) but `decided_at` is still null — there's no separate
  "recommended" status in your schema, so this is the cleanest existing
  signal.
- "My Screening Activity" for the Associate Editor is a proxy based on
  `updated_at` for manuscripts they're the associate editor on — there's
  no dedicated `screened_at` column in `manuscripts`, so this is an
  approximation of monthly screening activity, not an exact count of
  screening actions.
- Revenue only counts `journal_payments` rows with `status = completed`.
- All queries respect your existing `hasModulePermission()` /
  `isModuleAdmin()` / `isSuperAdmin()` logic — nothing new was added to
  `User.php`.
