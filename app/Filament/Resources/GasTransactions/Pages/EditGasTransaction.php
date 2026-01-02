<?php

namespace App\Filament\Resources\GasTransactions\Pages;

use App\Filament\Resources\GasTransactions\GasTransactionResource;
use App\Models\GasCylinder;
use App\Models\GasEvent;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EditGasTransaction extends EditRecord
{
    protected static string $resource = GasTransactionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit ' . $this->record->document_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $transactionId = $this->record->id;
        $cylinders = GasEvent::where('gas_transaction_id', $transactionId)->get();
        $data['gas_cylinder_id'] = $cylinders->pluck('gas_cylinder_id')->toArray();

        if ($cylinders->isNotEmpty()) {
            $firstCylinder = $cylinders->first();
            $data['to_location_id'] = $firstCylinder->to_location_id;
            $data['to_status'] = $firstCylinder->to_status;
        } else {
            $data['to_location_id'] = null;
            $data['to_status'] = null;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::transaction(function () use ($record, $data) {
            // Update the GasTransaction header fields
            $record->event_type = $data['event_type'] ?? $record->event_type;
            $record->document_number = $data['document_number'] ?? $record->document_number;
            $record->evidence_document = $data['evidence_document'] ?? $record->evidence_document;
            $record->notes = $data['notes'] ?? $record->notes;
            $record->save();

            //Update the Gas Event
            $selectedCylinderIds = Arr::wrap($data['gas_cylinder_id'] ?? []);

            $originalEvent = GasEvent::where('gas_transaction_id', $record->id)->first();
            $eventType = $data['event_type'] ?? null;
            $toLocation = $data['to_location_id'] ?? null;
            $toStatus = $data['to_status'] ?? null;

            $existingEvents = GasEvent::query()
                    ->where('gas_transaction_id', $record->id)
                    ->get();
            $existingCylinderIds = $existingEvents->pluck('gas_cylinder_id')->toArray();
            $deleteExistingCylinderIds = array_diff($existingCylinderIds, $selectedCylinderIds);

            if (!empty($deleteExistingCylinderIds)) {
                GasEvent::where('gas_transaction_id', $record->id)
                ->whereIn('gas_cylinder_id', $deleteExistingCylinderIds)
                ->delete();
            }

            foreach ($selectedCylinderIds as $cylinderId) {
                if ($existingEvents->has($cylinderId)) {
                    $event = $existingEvents[$cylinderId];

                    $event->update([
                        'event_type' => $eventType ?? $event->event_type,
                        'to_location_id' => $toLocation ?? $event->to_location_id,
                        'to_status' => $toStatus ?? $event->to_status,
                    ]);
                    $existingEvents->forget($cylinderId);
                } else {
                    GasEvent::create([
                        'id' => Str::ulid(),
                        'gas_transaction_id' => $record->id,
                        'gas_cylinder_id' => $cylinderId,
                        'event_type' => $record->event_type ?? $originalEvent->event_type,
                        'to_location_id' => $toLocation ?? $originalEvent->to_location_id,
                        'to_status' => $toStatus ?? $originalEvent->to_status,
                        'created_by' => Auth::id(),
            ]);
                }
            }
        });
        return $record;
    }
}
