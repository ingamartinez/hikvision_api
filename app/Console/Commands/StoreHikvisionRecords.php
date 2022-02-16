<?php

namespace App\Console\Commands;

use App\Services\Hikvision\HikvisionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StoreHikvisionRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hikvision:store
                            {--start-date= : Fecha de inicio de búsqueda [dd/mm/yyyy]}
                            {--end-date= : Fecha fin de búsqueda [dd/mm/yy]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Método que obtiene todos los registros biometricos y los guarda en BD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(HikvisionService $hikvisionService): int
    {
        if (!$this->isValidInputs()) return 1;

        $hikvisionService
            ->startDate(trim($this->option('start-date')))
            ->endDate(trim($this->option('end-date')))
        ->storeData();

        Log::alert('Se han guardado los registros con éxito');
        return 0;
    }

    public function isValidInputs(): bool
    {
        $validator = Validator::make([
            'start-date'    => trim($this->option('start-date')),
            'end-date'      => trim($this->option('end-date')),
        ], [
            'start-date'    => 'nullable|date_format:d-m-Y',
            'end-date'      => 'nullable|date_format:d-m-Y',
        ],[
            'date_format'    => 'El atributo [:attribute] no tiene un formato valido. Se espera formato [dd/mm/yyyy]',
        ]);
        if ($validator->fails()) {
            $this->info('Alguno de los argumentos no es válido');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }
        return true;
    }
}
