# Self-Registration Update — file placement

Copy this folder's contents straight into your `ora_platform` project root,
matching paths exactly (they mirror the Laravel structure):

```
database/migrations/2026_07_24_000000_add_self_registration_to_modules_table.php   → new file
database/seeders/ModuleSeeder.php                                                  → replaces existing
app/Models/Module.php                                                              → replaces existing
app/Services/ModuleEnrollmentService.php                                          → new file (new app/Services folder)
app/Http/Controllers/Auth/RegisterController.php                                  → replaces existing
app/Http/Controllers/ModuleEnrollmentController.php                               → new file
resources/views/auth/register.blade.php                                           → replaces existing
resources/views/modules/my-modules.blade.php                                      → new file
routes/routes_web_diff.md                                                         → NOT a real file — apply
                                                                                      the edits it describes to
                                                                                      your existing routes/web.php,
                                                                                      then delete this md file
```

## Also delete these two now-unused files from your project

- `app/Http/Controllers/Researcher/RegisterController.php`
- `resources/views/modules/researcher/public/register.blade.php`

## Then run

```bash
php artisan migrate
php artisan db:seed --class=ModuleSeeder
```

## Quick copy command (from inside this extracted folder)

```bash
cp -r database app app-src-check resources routes/. 2>/dev/null  # (illustrative — copy each subfolder over your project root)
rsync -av --exclude 'routes_web_diff.md' ./ /path/to/ora_platform/
```
(Review with `git diff` before committing — `rsync` will overwrite the files listed above.)
