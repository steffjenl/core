<?php

namespace Cachet\Listeners;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;

class UpdateListener
{
    public function __construct() {}

    public function handle(string $eventName, array $data)
    {
        // Does this class use the SendsWebhook trait?
        if (! empty($data) && isset($data[0]) && is_object($data[0]) && $data[0]->update->updateable instanceof \Cachet\Models\Incident) {
            $incident = $data[0]->update->updateable;

            // Check if the incident is resolved
            if (
                ($incident->updates()->latest()->first() instanceof \Cachet\Models\Update && $data[0]->update instanceof \Cachet\Models\Update)
                &&
                ($incident->updates()->latest()->first()->id == $data[0]->update->id)
            ) {
                // Update the incident status
                $incident->status = $data[0]->update->status;
                $incident->save();

                // If the incident is fixed, set all components to operational
                if ($data[0]->update->status == IncidentStatusEnum::fixed) {
                    foreach ($incident->components as $component) {
                        $component->status = ComponentStatusEnum::operational;
                        $component->save();
                    }
                }
            }
        }
    }
}
