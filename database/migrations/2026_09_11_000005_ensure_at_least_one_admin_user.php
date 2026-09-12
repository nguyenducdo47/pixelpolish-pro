<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        if (DB::table('users')->where('is_admin', true)->exists()) {
            return;
        }

        $id = DB::table('users')->orderBy('id')->value('id');

        if ($id) {
            DB::table('users')->where('id', $id)->update(['is_admin' => true]);
        }
    }

    public function down(): void
    {
        //
    }
};
