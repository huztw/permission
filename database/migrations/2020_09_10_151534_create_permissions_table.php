<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('permission.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('permission.database.roles_table'), function (Blueprint $table) {
            $table->id();
            $table->string('role', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->timestamps();
        });

        Schema::create(config('permission.database.permissions_table'), function (Blueprint $table) {
            $table->id();
            $table->string('permission', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->boolean('disable')->default(false);
            $table->timestamps();
        });

        Schema::create(config('permission.database.routes_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('http_method');
            $table->string('http_path');
            $table->string('visibility')->default('protected');
            $table->unique(['http_path', 'http_method'])->index();
            $table->timestamps();
        });

        Schema::create(config('permission.database.actions_table'), function (Blueprint $table) {
            $table->id();
            $table->string('action', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->string('visibility')->default('protected');
            $table->timestamps();
        });

        // Relationship
        Schema::create(config('permission.database.permission_roles_table'), function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained(config('permission.database.permissions_table'));
            $table->foreignId('role_id')->constrained(config('permission.database.roles_table'));
            $table->primary(['permission_id', 'role_id'])->index();
            $table->timestamps();
        });

        // Relationship
        Schema::create(config('permission.database.permission_routes_table'), function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained(config('permission.database.permissions_table'));
            $table->foreignId('route_id')->constrained(config('permission.database.routes_table'));
            $table->primary(['permission_id', 'route_id'])->index();
            $table->timestamps();
        });

        // Relationship
        Schema::create(config('permission.database.action_permissions_table'), function (Blueprint $table) {
            $table->foreignId('action_id')->constrained(config('permission.database.actions_table'));
            $table->foreignId('permission_id')->constrained(config('permission.database.permissions_table'));
            $table->primary(['action_id', 'permission_id'])->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Relationship
        Schema::dropIfExists(config('permission.database.permission_roles_table'));
        Schema::dropIfExists(config('permission.database.permission_routes_table'));
        Schema::dropIfExists(config('permission.database.action_permissions_table'));

        Schema::dropIfExists(config('permission.database.roles_table'));
        Schema::dropIfExists(config('permission.database.permissions_table'));
        Schema::dropIfExists(config('permission.database.routes_table'));
        Schema::dropIfExists(config('permission.database.actions_table'));
    }
}
