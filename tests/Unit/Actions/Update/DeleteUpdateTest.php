<?php

use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can delete an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    app(DeleteUpdate::class)->handle($update);

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $update->updateable_id,
    ]);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateDeleted::class, fn ($event) => $event->update->is($update));
});

it('can delete a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    app(DeleteUpdate::class)->handle($update);

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(\Cachet\Models\Schedule::class),
        'updateable_id' => $update->updateable_id,
    ]);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateDeleted::class, fn ($event) => $event->update->is($update));
});
