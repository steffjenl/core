<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can create an incident update', function () {
    $incident = Incident::factory()->create();

    $data = CreateIncidentUpdateRequestData::from([
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateCreated::class, fn ($event) => $event->update->is($incidentUpdate));
});

it('an incident\'s computed latest status equals the new status', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    $data = CreateIncidentUpdateRequestData::from([
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::identified,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message)
        ->and($incident->fresh())
        ->latestStatus->toEqual(IncidentStatusEnum::identified);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateCreated::class, fn ($event) => $event->update->is($incidentUpdate));
});

it('can create a schedule update', function () {
    $schedule = Schedule::factory()->create();

    $data = CreateScheduleUpdateRequestData::from([
        'message' => 'This is an update message for a schedule.',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($schedule, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message);

    Event::assertDispatched(\Cachet\Events\Updates\UpdateCreated::class, fn ($event) => $event->update->is($incidentUpdate));
});
