# ORA Platform — Digital Library Payment Integration

Full CRUD pricing plans + Chapa checkout for paid digital resources
(ebooks, journal articles, papers, other), for the ORA Platform
Digital Library module.

## What this adds

- **`library_pricing_plans`** — a full CRUD list of fee rules (name,
  optional resource-type scope, amount, currency, active/inactive)
  managed by the Library Manager (`manage-settings`), under
  **Library → Pricing Plans** in the sidebar.
- **`library_digital_resources.pricing_plan_id`** — a Digital
  Librarian assigns one of these plans (or none = free) when
  uploading/editing a resource.
- **`library_resource_purchases`** — one row per Chapa checkout
  attempt; snapshots the amount/currency actually charged so a later
  change to the plan (or its deletion) never rewrites history.
- A public checkout flow: a paid resource shows a "Buy Access" button
  instead of "Download" until the logged-in user has a completed
  purchase. Buying auto-enrolls the user into the Library module if
  they aren't already (same pattern as the existing hold/reserve
  flow) — no separate sign-up needed first.
- Staff with `manage-digital-collection`, and a resource's own owner
  (`submit-digital-content`), always bypass payment for their own
  reviewing/uploading, exactly like the rest of the module already
  works for access tiers.

## How to apply

1. Copy every file in this archive into your `ora_platform` working
   copy, preserving paths (they overlay directly onto
   `app/`, `database/`, `resources/`, `routes/`, `bootstrap/`).
   Two files (`app/Models/LibraryDigitalResource.php`,
   `app/Http/Controllers/Library/DigitalResourceController.php`,
   `app/Http/Controllers/Library/PublicController.php`,
   `app/Services/SidebarService.php`, `routes/web.php`,
   `bootstrap/app.php`, `database/seeders/RoleSeeder.php`) are edited
   copies of files that already exist in your repo — they fully
   replace the originals, they aren't diffs to merge by hand.

2. Run the new migrations:
   ```
   php artisan migrate
   ```

3. Re-run the role seeder so Library Manager picks up the new
   `manage-payments` permission (used to notify staff when a purchase
   completes):
   ```
   php artisan db:seed --class=RoleSeeder
   ```
   This seeder uses `updateOrCreate`/`sync`, so it's safe to re-run —
   it won't duplicate or reset anything else.

4. Make sure your `.env` already has (it should, if Ebook/Journal
   payments work today):
   ```
   CHAPA_SECRET_KEY=...
   CHAPA_PUBLIC_KEY=...
   ```

5. Create a pricing plan: **Library → Pricing Plans → New Pricing
   Plan**. Then, on any digital resource's create/edit form, pick it
   under the new **Pricing** card.

6. For the Chapa webhook to actually confirm payments
   (`library/digital-resources/payments/chapa/webhook`), your server
   needs to be reachable from the internet — same requirement as your
   existing Ebook/Journal webhooks, so if those work in production
   already, this will too. Locally, the "return" redirect after
   checkout will also attempt to verify+settle the payment as a
   convenience, but the webhook is the real source of truth.

## Files in this archive

```
app/Http/Controllers/Library/DigitalResourceController.php   (edited)
app/Http/Controllers/Library/DigitalResourcePaymentController.php (new)
app/Http/Controllers/Library/PricingPlanController.php        (new)
app/Http/Controllers/Library/PublicController.php              (edited)
app/Models/LibraryDigitalResource.php                          (edited)
app/Models/LibraryPricingPlan.php                              (new)
app/Models/LibraryResourcePurchase.php                         (new)
app/Services/SidebarService.php                                (edited)
bootstrap/app.php                                               (edited)
database/migrations/2027_07_28_000100_create_library_pricing_plans_table.php
database/migrations/2027_07_28_000200_add_pricing_plan_id_to_library_digital_resources_table.php
database/migrations/2027_07_28_000300_create_library_resource_purchases_table.php
database/seeders/RoleSeeder.php                                (edited)
resources/views/modules/library/digital-resources/create.blade.php (edited)
resources/views/modules/library/digital-resources/edit.blade.php   (edited)
resources/views/modules/library/digital-resources/index.blade.php  (edited)
resources/views/modules/library/digital-resources/payment.blade.php (new)
resources/views/modules/library/digital-resources/show.blade.php    (edited)
resources/views/modules/library/pricing-plans/create.blade.php (new)
resources/views/modules/library/pricing-plans/edit.blade.php   (new)
resources/views/modules/library/pricing-plans/index.blade.php  (new)
resources/views/modules/library/public/digital-index.blade.php (edited)
resources/views/modules/library/public/digital-show.blade.php  (edited)
routes/web.php                                                  (edited)
```
