<?php

namespace Displore\Core\Notifications;

use Displore\Core\Models\Notification;
use Illuminate\Session\Store;

class Notifier
{
    /**
     * Session storage.
     * 
     * @var \Illuminate\Session\Store
     */
    protected $store;

    /**
     * Notifications repository.
     * 
     * @var \Displore\Core\Notifications\Notification
     */
    protected $repository;

    /**
     * Create a new Notifier instance.
     * 
     * @param \Illuminate\Session\Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Create a new notification, defaults to type user and an unread status.
     * 
     * @param string $message
     * @param int    $id
     * @param string $type
     * @param bool   $read
     *
     * @return static
     */
    public function notify($message, $id, $type = 'user', $read = false)
    {
        return Notification::create([
            'message' => $message,
            'type' => $type,
            'type_id' => $id,
            'read' => $read,
        ]);
    }

    /**
     * Flash a notification to the session storage.
     * These can be used in the views as the $notification variable.
     * 
     * @param string $message
     * @param mixed  $metadata
     */
    public function flash($message, $metadata = null)
    {
        $data = [
            'message' => $message,
            'metadata' => $metadata,
        ];
        $this->store->flash('notification', $data);
    }

    /**
     * Get access to the repository to interact with existing notifications.
     */
    public function get()
    {
        if (!isset($this->repository)) {
            $this->repository = new Repository();
        }

        return $this->repository;
    }
}
