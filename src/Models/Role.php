<?php

namespace Huztw\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $fillable = ['role', 'name'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('permission.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('permission.database.roles_table'));

        parent::__construct($attributes);
    }

    /**
     * A role belongs to many permissions.
     *
     * @return Permission
     */
    public function permissions()
    {
        $pivotTable = config('permission.database.permission_roles_table');

        $relatedModel = config('permission.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id')->withTimestamps();
    }

    /**
     * Check role has permission.
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

        $can = $this->permissions()->get()->first(function ($item) use ($permissions) {
            return Str::is($permissions, $item->permission) || Str::is($item->permission, $permissions);
        });

        return $can ? true : false;
    }

    /**
     * Check role has no permission.
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
            $model->administrators()->detach();
            $model->permissions()->detach();
        });
    }
}
