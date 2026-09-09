<?php

namespace App\Jobs;

use App\Models\CargoDetail;
use App\Models\ContainerTrackingEvent;
use App\Models\FastagTransaction;
use App\Services\UlipProxyClient;
use App\Support\UlipIdentifierValidation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Polls the ULIP proxy for container/FASTag data on every dispatch still in
 * progress. See docs/adr/0003-ulip-container-fastag-polling.md for why:
 * polling (not on-demand), a 6h cadence, local format validation before
 * calling, and append-only deduped history.
 */
class FetchUlipDataForActiveDispatches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(UlipProxyClient $client): void
    {
        CargoDetail::where(function ($q) {
            $q->whereNull('pending_servey')->orWhere('pending_servey', 0);
        })
            ->chunkById(100, function ($cargoDetails) use ($client) {
                foreach ($cargoDetails as $cargoDetail) {
                    $this->fetchFastagFor($cargoDetail, $client);
                    $this->fetchContainerFor($cargoDetail, $client);
                }
            });
    }

    private function fetchFastagFor(CargoDetail $cargoDetail, UlipProxyClient $client): void
    {
        if (!UlipIdentifierValidation::isValidVehicleNumber($cargoDetail->veh_reg_no)) {
            return;
        }

        $data = $client->fetchFastag($cargoDetail->veh_reg_no);

        $this->storeEntries(FastagTransaction::class, $cargoDetail, $data);
    }

    private function fetchContainerFor(CargoDetail $cargoDetail, UlipProxyClient $client): void
    {
        if (!UlipIdentifierValidation::isValidContainerNumber($cargoDetail->cargo_unit_serial_no)) {
            return;
        }

        $data = $client->fetchContainer($cargoDetail->cargo_unit_serial_no);

        $this->storeEntries(ContainerTrackingEvent::class, $cargoDetail, $data);
    }

    /**
     * The gov payload's real shape isn't modeled anywhere (ulip-apis
     * passes it through untouched) - it may be a single object or a list
     * of entries (e.g. toll transactions). Either way, each entry becomes
     * its own deduped history row.
     */
    private function storeEntries(string $model, CargoDetail $cargoDetail, ?array $data): void
    {
        if ($data === null) {
            return;
        }

        $entries = array_is_list($data) ? $data : [$data];

        foreach ($entries as $entry) {
            $model::firstOrCreate(
                [
                    'cargo_detail_id' => $cargoDetail->id,
                    'dedupe_hash' => hash('sha256', json_encode($entry)),
                ],
                [
                    'payload' => $entry,
                    'fetched_at' => now(),
                ],
            );
        }
    }
}
