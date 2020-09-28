<?php

namespace Huztw\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Action extends Model
{
    protected $fillable = ['action', 'name', 'visibility'];

    /**
     * @var array
     */
    protected static $visibility = [
        'public'    => 'public',
        'protected' => 'protected',
        'private'   => 'private',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'visibility' => 'protected',
    ];

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
        return config('permission.database.actions_table');
    }

    /**
     * A action belongs to many permissions.
     *
     * @return Permission
     */
    public function permissions()
    {
        $pivotTable = config('permission.database.action_permissions_table');

        $relatedModel = config('permission.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'action_id', 'permission_id')->withTimestamps();
    }

    /**
     * Get visibility is Public.
     *
     * @return string
     */
    public static function getPublic()
    {
        return self::$visibility['public'];
    }

    /**
     * Get visibility is protected.
     *
     * @return string
     */
    public static function getProtected()
    {
        return self::$visibility['protected'];
    }

    /**
     * Get visibility is private.
     *
     * @return string
     */
    public static function getPrivate()
    {
        return self::$visibility['private'];
    }

    /**
     * Determine if the action can pass through.
     *
     * @param string|array|null $actions
     *
     * @return bool
     */
    public function can($actions): bool
    {
        if (is_array($actions)) {
            $reject = collect($actions)->map(function ($action, $key) {
                return $this->can($action);
            })->reject()->count();

            return ($reject == 0) ? true : false;
        }

        return Str::is($actions, $this->action) || Str::is($this->action, $actions);
    }

    /**
     * Determine if the action can not pass.
     *
     * @param string|array|null $actions
     *
     * @return bool
     */
    public function cannot($actions): bool
    {
        return !$this->can($actions);
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
            $model->permissions()->detach();
        });
    }
}
