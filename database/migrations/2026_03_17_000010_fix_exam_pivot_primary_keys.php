<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Only apply for MySQL/MariaDB (common in WAMP/XAMPP)
        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $this->addCompositePrimaryIfMissing('chitietketqua', ['makq', 'macauhoi']);
        $this->addCompositePrimaryIfMissing('giaodethi', ['made', 'manhom']);
        $this->addCompositePrimaryIfMissing('dethitudong', ['made', 'machuong']);
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $this->dropPrimaryIfExists('chitietketqua');
        $this->dropPrimaryIfExists('giaodethi');
        $this->dropPrimaryIfExists('dethitudong');
    }

    private function addCompositePrimaryIfMissing(string $table, array $columns): void
    {
        if ($this->hasPrimaryKey($table)) {
            return;
        }

        $cols = implode('`,`', $columns);
        DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`{$cols}`)");
    }

    private function dropPrimaryIfExists(string $table): void
    {
        if (!$this->hasPrimaryKey($table)) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
    }

    private function hasPrimaryKey(string $table): bool
    {
        $res = DB::selectOne("
            SELECT COUNT(*) AS cnt
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND CONSTRAINT_TYPE = 'PRIMARY KEY'
        ", [$table]);

        return ((int)($res->cnt ?? 0)) > 0;
    }
};

