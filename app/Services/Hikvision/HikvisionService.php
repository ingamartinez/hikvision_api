<?php

namespace App\Services\Hikvision;

use App\Models\Hikvision;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class HikvisionService implements HikvisionInterface
{

    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(protected Client $client, protected string $url)
    {
        $this->startDate = Carbon::yesterday()->startOfDay();
        $this->endDate = Carbon::yesterday()->endOfDay();
    }

    public function storeData () {
        $data = $this->getData();
        $dataNormalized = $this->normalizeData($data);
    }

    public function insertData(array $dataNormalized) {
        foreach ($dataNormalized as $item) {
            Hikvision::updateOrCreate($item, ['userId','title','completed','date']);
        }
        return true;
    }

    public function normalizeData($data): array
    {
        $addDay = Carbon::now()->subDays(3);
        return array_map(function ($item) use ($addDay) {
            $item->date = $addDay->toDateString();
            unset($item->id);
            return (array) $item;
        }, $data);
    }

    public function getData()
    {
        try {
            $res = $this->client->request('GET', $this->url,
                [
                    'verify' => false,
                    'synchronous' => true,
                    'max_retry_attempts' => 10,
                    'retry_on_status' => [429, 500, 501, 502, 503],
                    'retry_on_timeout' => true,
                    'default_retry_multiplier' => 10,
                    'on_retry_callback' => $this->getListener(),
                ]
            );
            return json_decode($res->getBody()->getContents());

        } catch (GuzzleException $e) {
            Log::error('connection_error');
            Log::error('message ' . $e->getMessage());
        }
    }


    protected function getListener(): \Closure
    {
        return function ($attemptNumber) {
            Log::alert("Retrying: Attempt #" . $attemptNumber);
        };
    }

    public function startDate(string $startDate): static
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        return $this;
    }

    public function endDate(string $endDate): static
    {
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        return $this;
    }
}
