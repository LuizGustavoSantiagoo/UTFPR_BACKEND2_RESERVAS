<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('born_date')->nullable()->after('email');
            $table->enum('role', array_column(Role::cases(), 'value'))->default(Role::User->value)->after('born_date');
        });

        // usuários já existentes recebem uma data provisória para permitir o NOT NULL
        DB::table('users')->whereNull('born_date')->update(['born_date' => '2000-01-01']);

        Schema::table('users', function (Blueprint $table) {
            $table->date('born_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['born_date', 'role']);
        });
    }
};
