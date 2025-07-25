<?php

use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\Requests\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Models\Update;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can update an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    $data = EditIncidentUpdateRequestData::from([
        'message' => 'Incident Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateUpdated::class, fn ($event) => $event->update->is($update));
});

it('can update a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    $data = EditScheduleUpdateRequestData::from([
        'message' => 'Schedule Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateUpdated::class, fn ($event) => $event->update->is($update));
});
