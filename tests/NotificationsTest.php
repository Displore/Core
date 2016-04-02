<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->notifier = app('Displore\Core\Notifications\Notifier');
    }

    public function test_basic_notify_for_a_user()
    {
        $this->notifier->notify('test notify message', 1);

        $this->seeInDatabase('notifications', ['message' => 'test notify message']);
    }

    public function test_extensive_notify_for_a_task()
    {
        $this->notifier->notify('test notify message', 1, 'task', true);

        $data = [
            'message' => 'test notify message',
            'type' => 'task',
            'type_id' => 1,
            'read' => true,
            ];

        $this->seeInDatabase('notifications', $data);
    }

    public function test_basic_flash_notification()
    {
        $this->notifier->flash('test flash notify message');

        $this->assertSessionHas('notification', ['message' => 'test flash notify message', 'metadata' => null]);
    }

    public function test_extensive_flash_notification()
    {
        $this->notifier->flash('test flash notify message', ['id' => 1]);

        $this->assertSessionHas('notification', ['message' => 'test flash notify message', 'metadata' => ['id' => 1]]);
    }

    public function test_get_repository()
    {
        $r = $this->notifier->get();

        $this->assertInstanceOf('Displore\Core\Notifications\Repository', $r);
    }

    public function test_get_all_for_a_task()
    {
        $this->notifier->notify('test notify message', 1, 'task');

        $n = $this->notifier->get()->allFor('task', 1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_get_all_for_a_user()
    {
        $this->notifier->notify('test notify message', 1);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_get_all_unread_for_a_task()
    {
        $this->notifier->notify('test notify message', 1, 'task');

        $n = $this->notifier->get()->allUnreadFor('task', 1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_get_all_unread_for_a_user()
    {
        $this->notifier->notify('test notify message', 1);

        $n = $this->notifier->get()->allUnreadForUser(1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_get_all_unread_since()
    {
        $this->notifier->notify('test notify message', 1);

        $timestamp = Carbon\Carbon::now()->subDays(1);

        $n = $this->notifier->get()->allUnreadSince($timestamp, 'user', 1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_get_all_read_for_a_task()
    {
        $n = $this->notifier->notify('test notify message', 1, 'task', true);

        $result = $this->notifier->get()->allReadFor('task', 1)->isEmpty();

        $this->assertFalse($result);
    }

    public function test_get_all_read_for_a_user()
    {
        $this->notifier->notify('test notify message', 1, 'user', true);

        $n = $this->notifier->get()->allReadForUser(1)->isEmpty();

        $this->assertFalse($n);
    }

    public function test_mark_as_read()
    {
        $n = $this->notifier->notify('test notify message', 1);

        $id = $n->id;

        $this->notifier->get()->markAsRead($id);

        $this->seeInDatabase('notifications', ['id' => $id, 'read' => 1]);
    }

    public function test_mark_as_unread()
    {
        $n = $this->notifier->notify('test notify message', 1, 'user', true);

        $id = $n->id;

        $this->notifier->get()->markAsUnread($id);

        $this->seeInDatabase('notifications', ['id' => $id, 'read' => 0]);
    }

    public function test_mark_all_as_read()
    {
        $n = $this->notifier->notify('test notify message', 1);

        $id = $n->id;

        $this->notifier->get()->markAllAsReadFor('user', 1);

        $this->seeInDatabase('notifications', ['id' => $id, 'read' => 1]);
    }

    public function test_flush_read_older_than()
    {
        $this->notifier->notify('test notify message', 1, 'user', true);

        $timestamp = Carbon\Carbon::now()->addHours(1)->format('Y-m-d H:i:s');

        $this->notifier->get()->flushOlderThan($timestamp);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertTrue($n);
    }

    public function test_flush_unread_older_than()
    {
        $this->notifier->notify('test notify message', 1, 'user', false);

        $timestamp = Carbon\Carbon::now()->addHours(1)->format('Y-m-d H:i:s');

        $this->notifier->get()->flushOlderThan($timestamp, false);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertTrue($n);
    }

    public function test_flush_all_read_for_a_task()
    {
        $n = $this->notifier->notify('test notify message', 1, 'task', true);

        $this->notifier->get()->flushAllFor('task', 1);

        $result = $this->notifier->get()->allFor('task', 1)->isEmpty();

        $this->assertTrue($result);
    }

    public function test_flush_all_unread_for_a_task()
    {
        $n = $this->notifier->notify('test notify message', 1, 'task');

        $this->notifier->get()->flushAllFor('task', 1, false);

        $result = $this->notifier->get()->allFor('task', 1)->isEmpty();

        $this->assertTrue($result);
    }

    public function test_flush_all_read_for_a_user()
    {
        $this->notifier->notify('test notify message', 1, 'user', true);

        $this->notifier->get()->flushAllForUser(1);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertTrue($n);
    }

    public function test_flush_all_unread_for_a_user()
    {
        $this->notifier->notify('test notify message', 1, 'user');

        $this->notifier->get()->flushAllForUser(1, false);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertTrue($n);
    }

    public function test_flush_old_and_read()
    {
        $this->notifier->notify('test notify message', 1, 'user', true);

        $timestamp = Carbon\Carbon::now()->addHours(1)->format('Y-m-d H:i:s');

        $this->notifier->get()->flushOldAndReadFor($timestamp, 'user', 1);

        $n = $this->notifier->get()->allForUser(1)->isEmpty();

        $this->assertTrue($n);
    }
}
