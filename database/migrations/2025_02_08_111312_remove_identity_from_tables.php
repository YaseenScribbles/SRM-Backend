<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = ['contacts', 'distributors', 'orders', 'purposes', 'users', 'visits'];

    public function up(): void
    {

        try {
            foreach ($this->tables as $table) {
                // Step 0: Drop Foreign Keys first
                $foreignKeys = $this->getForeignKeys($table);
                if ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        DB::statement("ALTER TABLE $fk->parent_table DROP CONSTRAINT $fk->fk_name");
                    }
                }

                // Step 1: Drop Primary Key Constraint
                $primaryKey = $this->getPrimaryKey($table);
                if ($primaryKey) {
                    DB::statement("ALTER TABLE $table DROP CONSTRAINT $primaryKey");
                }

                // Step 2: Add a new column without IDENTITY
                Schema::table($table, function (Blueprint $table) {
                    $table->integer('new_id')->default(0);
                });

                // Step 3: Copy existing values
                DB::statement("UPDATE $table SET new_id = id");

                // Step 4: Drop the old IDENTITY column
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('id');
                });

                // Step 5: Rename `new_id` to `id`
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('new_id', 'id');
                });

                // Step 6: Re-add the primary key
                Schema::table($table, function (Blueprint $table) {
                    $table->primary('id');
                });

                // Step 7: Restore Foreign Keys
                if ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        DB::statement("ALTER TABLE $fk->parent_table ADD CONSTRAINT $fk->fk_name FOREIGN KEY ($fk->parent_column) REFERENCES $fk->referenced_table($fk->referenced_column)");
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function down(): void
    {
        try {

            foreach ($this->tables as $table) {
                // Drop Foreign Keys first
                $foreignKeys = $this->getForeignKeys($table);
                if ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        DB::statement("ALTER TABLE $fk->parent_table DROP CONSTRAINT $fk->fk_name");
                    }
                }

                // Drop Primary Key Constraint
                $primaryKey = $this->getPrimaryKey($table);
                if ($primaryKey) {
                    DB::statement("ALTER TABLE $table DROP CONSTRAINT $primaryKey");
                }

                // Step 1: Add `new_id` with IDENTITY
                Schema::table($table, function (Blueprint $table) {
                    $table->integer('new_id')->autoIncrement();
                });

                // Step 2: Copy data back
                DB::statement("UPDATE $table SET new_id = id");

                // Step 3: Drop old column
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('id');
                });

                // Step 4: Rename column back to `id`
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('new_id', 'id');
                });

                // Step 5: Re-add primary key
                Schema::table($table, function (Blueprint $table) {
                    $table->primary('id');
                });

                // Restore Foreign Keys
                if ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        DB::statement("ALTER TABLE $fk->parent_table ADD CONSTRAINT $fk->fk_name FOREIGN KEY ($fk->parent_column) REFERENCES $fk->referenced_table($fk->referenced_column)");
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Get foreign key constraints for a table.
     */
    private function getForeignKeys(string $table): array
    {
        return DB::select("
        SELECT
            fk.name AS fk_name,
            tp.name AS parent_table,
            cp.name AS parent_column,
            tr.name AS referenced_table,
            cr.name AS referenced_column
        FROM sys.foreign_keys fk
        INNER JOIN sys.foreign_key_columns fkc ON fk.object_id = fkc.constraint_object_id
        INNER JOIN sys.tables tp ON fkc.parent_object_id = tp.object_id
        INNER JOIN sys.columns cp ON fkc.parent_object_id = cp.object_id AND fkc.parent_column_id = cp.column_id
        INNER JOIN sys.tables tr ON fkc.referenced_object_id = tr.object_id
        INNER JOIN sys.columns cr ON fkc.referenced_object_id = cr.object_id AND fkc.referenced_column_id = cr.column_id
        WHERE tr.name = ? OR tp.name = ?
    ", [$table, $table]);
    }

    /**
     * Get primary key constraint name for a table.
     */
    private function getPrimaryKey(string $table): ?string
    {
        $result = DB::select("
            SELECT kc.name AS constraint_name
            FROM sys.key_constraints kc
            INNER JOIN sys.tables t ON kc.parent_object_id = t.object_id
            WHERE kc.type = 'PK' AND t.name = ?
        ", [$table]);

        return $result[0]->constraint_name ?? null;
    }
};
