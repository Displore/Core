<?php

namespace Displore\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'type', 'type_id', 'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Eloquent Scope for read/unread notifications.
     * 
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  bool                               $flag
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRead($query, $flag)
    {
        return $query->where('read', '=', $flag);
    }

    /**
     * Eloquent Scope for the notification type.
     * 
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string                             $type
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    /**
     * Eloquent Scope for the notification type id.
     * 
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $typeId
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeTypeId($query, $typeId)
    {
        return $query->where('type_id', '=', $typeId);
    }
}
