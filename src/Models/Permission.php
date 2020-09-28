<?php

namespace Huztw\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['permission', 'name', 'disable'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('permission.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(self::table());

        parent::__construct($attributes);
    }

    /**
     * Database table.
     *
     * @return string
     */
    public static function table()
    {
        return config('permission.database.permissions_table');
    }

    /**
     * A permission belongs to many roles.
     *
     * @return Role
     */
    public function roles()
    {
        $pivotTable = config('permission.database.permission_roles_table');

        $relatedModel = config('permission.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id')->withTimestamps();
    }

    /**
     * A permission belongs to many routes.
     *
     * @return Route
     */
    public function routes()
    {
        $pivotTable = config('permission.database.permission_routes_table');

        $relatedModel = config('permission.database.routes_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'route_id')->withTimestamps();
    }

    /**
     * A permission belongs to many actions.
     *
     * @return Action
     */
    public function actions()
    {
        $pivotTable = config('permission.database.action_permissions_table');

        $relatedModel = config('permission.database.actions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'action_id')->withTimestamps();
    }

    /**
     * @param $get
     *
     * @return bool
     */
    public function getDisableAttribute($get)
    {
        return boolval($get);
    }

    /**
     * Determine if the permission can pass through.
     *
     * @param string|array|null $permissions
     *
     * @return bool
     */
    public function can($permissions): bool
    {
        if (is_array($permissions)) {
            $reject = collect($permissions)->map(function ($permission, $key) {
                return $this->can($permission);
            })->reject()->count();

            return ($reject == 0) ? true : false;
        }

        return Str::is($permissions, $this->permission) || Str::is($this->permission, $permissions);
    }

    /**
     * Determine if the permission can not pass.
     *
     * @param string|array|null $permissions
     *
     * @return bool
     */
    public function cannot($permissions): bool
    {
        return !$this->can($permissions);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();
            $model->routes()->detach();
            $model->actions()->detach();
        });
    }
}
