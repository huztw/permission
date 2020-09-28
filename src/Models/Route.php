<?php

namespace Huztw\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route as BaseRoute;
use Illuminate\Support\Str;

class Route extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['http_path', 'http_method', 'name', 'visibility'];

    /**
     * @var array
     */
    public static $httpMethods = [
        'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT',
    ];

    /**
     * @var array
     */
    public static $exceptMethods = [
        'HEAD', 'OPTIONS',
    ];

    /**
     * @var array
     */
    protected static $visibility = [
        'public'    => 'public',
        'protected' => 'protected',
        'private'   => 'private',
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

        $this->setTable(config('permission.database.routes_table'));

        parent::__construct($attributes);
    }

    /**
     * A route belongs to many permissions.
     *
     * @return Permission
     */
    public function permissions()
    {
        $pivotTable = config('permission.database.permission_routes_table');

        $relatedModel = config('permission.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'route_id', 'permission_id')->withTimestamps();
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
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
     * Determine if the route is match with pattern.
     *
     * @param string $pattern
     * @param string $route
     *
     * @return bool
     */
    public static function isMatch($pattern, $route): bool
    {
        $route = rawurldecode($route);

        return Str::is($pattern, $route) || Str::is($route, $pattern);
    }

    /**
     * Determine if the route can pass through.
     *
     * @param string|array|null $routes
     *
     * @return bool
     */
    public function can($routes): bool
    {
        if (is_array($routes)) {
            $reject = collect($routes)->map(function ($route, $key) {
                return $this->can($route);
            })->reject()->count();

            return ($reject == 0) ? true : false;
        }

        return self::isMatch($this->http_path, $routes);
    }

    /**
     * Determine if the route can not pass.
     *
     * @param string|array|null $routes
     *
     * @return bool
     */
    public function cannot($routes): bool
    {
        return !$this->can($routes);
    }

    /**
     * Get the match and have high similarity route.
     *
     * @param Request|null $request
     *
     * @return Route|null
     */
    public static function similarRoute(Request $request = null)
    {
        if (!$request) {
            $request = request();
        }

        $similar = 0;

        foreach (self::all() as $item) {
            if (self::isMatch($request->path(), $item->http_path)) {
                if (in_array($request->method(), $item->http_method)) {
                    similar_text($request->path(), $item->http_path, $newSimilar);

                    if ($newSimilar > $similar) {
                        $similar = $newSimilar;
                        $route   = $item;
                    }
                }
            }
        }

        return $route ?? null;
    }

    /**
     * Get route.
     *
     * @return RouteCollection
     */
    public static function getRoutes()
    {
        return BaseRoute::getRoutes();
    }
}
