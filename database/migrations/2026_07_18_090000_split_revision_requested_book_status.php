<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The book workflow used to have a single 'revision_requested'
     * status for the Book Editor's editorial decision. That's now
     * split into distinct 'minor_revision' and 'major_revision'
     * statuses (see App\Models\Book::STATUSES), so that whichever one
     * an editor picks, the author still lands on the same "revise and
     * resubmit" screen (see App\Models\Book::REVISABLE_STATUSES).
     *
     * Any book already sitting at the old generic status is backfilled
     * to 'major_revision' — the safer of the two, since it keeps the
     * author's required action ("this needs work before it can move
     * on") intact even though the original minor/major distinction
     * wasn't recorded at the book level.
     */
    public function up(): void
    {
        DB::table('books')
            ->where('status', 'revision_requested')
            ->update(['status' => 'major_revision']);
    }

    public function down(): void
    {
        DB::table('books')
            ->whereIn('status', ['minor_revision', 'major_revision'])
            ->update(['status' => 'revision_requested']);
    }
};
