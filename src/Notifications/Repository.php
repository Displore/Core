<?php

namespace Displore\Core\Notifications;

use Displore\Core\Models\Notification;

class Repository
{
    /**
     * Get all notifications for a given type and id.
     * 
     * @param string $type
     * @param int    $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allFor($type, $id)
    {
        return Notification::type($type)->typeId($id)->get();
    }

    /**
     * Shortcut to get all notifications for a user.
     * 
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allForUser($userId)
    {
        return $this->allFor('user', $userId);
    }

    /**
     * Get all unread notifications for a given type and id.
     * 
     * @param string $type
     * @param int    $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allUnreadFor($type, $id)
    {
        return Notification::type($type)->typeId($id)->read(false)->get();
    }

    /**
     * Shortcut to get all unread notifications for a given user.
     * 
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allUnreadForUser($userId)
    {
        return $this->allUnreadFor('user', $userId);
    }

    /**
     * Get all unread notifications for a given type and id since a given time.
     * 
     * @param \DateTime $timestamp
     * @param string    $type
     * @param int       $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allUnreadSince($timestamp, $type, $id)
    {
        return Notification::type($type)
                            ->typeId($id)
                            ->read(false)
                            ->where('updated_at', '>', $timestamp)
                            ->get();
    }

    /**
     * Get al read notifications for a given type and id.
     * 
     * @param string $type
     * @param int    $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allReadFor($type, $id)
    {
        return Notification::type($type)->typeId($id)->read(true)->get();
    }

    /**
     * Shortcut to get all read notifications for a given user.
     * 
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allReadForUser($userId)
    {
        return $this->allReadFor('user', $userId);
    }

    /**
     * Mark a given notification as read.
     * 
     * @param int $notificationId
     *
     * @return bool|int
     */
    public function markAsRead($notificationId)
    {
        return Notification::where('id', $notificationId)->update(['read' => true]);
    }

    /**
     * Mark a given notification as unread.
     * 
     * @param int $notificationId
     *
     * @return bool|int
     */
    public function markAsUnread($notificationId)
    {
        return Notification::where('id', $notificationId)->update(['read' => false]);
    }

    /**
     * Mark all as read for a given type and id.
     * 
     * @param string $type
     * @param int    $id
     *
     * @return bool|int
     */
    public function markAllAsReadFor($type, $id)
    {
        return Notification::type($type)->typeId($id)->update(['read' => true]);
    }

    /**
     * Flush all notifications older than a given timestamp.
     * Optional flag decides if only read messages are deleted, defaults to true.
     * 
     * @param \DateTime $timestamp
     * @param bool      $read
     *
     * @return bool|null
     */
    public function flushOlderThan($timestamp, $read = true)
    {
        return Notification::read($read)
                            ->where('updated_at', '<', $timestamp)
                            ->delete();
    }

    /**
     * Flush all notifications for a given type and id.
     * Optional flag decides if only read messages are deleted, defaults to true.
     * 
     * @param string $type
     * @param int    $id
     * @param bool   $read
     *
     * @return bool|null
     */
    public function flushAllFor($type, $id, $read = true)
    {
        return Notification::type($type)->typeId($id)->read($read)->delete();
    }

    /**
     * Shortcut to flush all notifications for a given user.
     * Optional flag decides if only read messages are deleted, defaults to true.
     * 
     * @param int  $userId
     * @param bool $read
     *
     * @return bool|null
     */
    public function flushAllForUser($userId, $read = true)
    {
        return $this->flushAllFor('user', $userId, $read);
    }

    /**
     * Flush all notifications for a given type and id  that are read and older.
     * 
     * @param \DateTime $timestamp
     * @param string    $type
     * @param int       $id
     *
     * @return bool|null
     */
    public function flushOldAndReadFor($timestamp, $type, $id)
    {
        return Notification::type($type)
                            ->typeId($id)
                            ->read(true)
                            ->where('updated_at', '<', $timestamp)
                            ->delete();
    }

    /**
     * Get the Eloquent Notification model to perform queries.
     * 
     * @return \Displore\Core\Notifications\Notification
     */
    public function eloquent()
    {
        return new Notification();
    }
}
