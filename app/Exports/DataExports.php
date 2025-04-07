<?php

namespace App\Exports;

use App\Exports\Sheets\DataSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Excel;

/**
 * @template TModel
 */
class DataExports implements WithMultipleSheets, WithProperties
{
    use Exportable;

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional Disk
     */
    private $disk = 'protected';

    /**
     * Optional Disk OPtions
     */
    private $diskOptions = [
        'visibility' => 'public',
    ];

    /**
     * @param array{id:string,model:class-string<TModel>,model_id:string|int|null,keywords:string,name:string,columns:array<int,string>} $exportable
     * @param int $perPage
     *
     */
    public function __construct(
        protected array $exportable,
        protected int $perPage = 50,
    ) {}

    public function sheets(): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder $dataQuery */
        $dataQuery = $this->exportable['model']::query();

        if (!empty($this->exportable['model_id'])) {
            $keyName = (new $this->exportable['model'])->getKeyName();

            $key = is_array($this->exportable['model_id'])
                ? $this->exportable['model_id']
                : [$this->exportable['model_id']];

            $dataQuery->whereIn($keyName, $key);
        }

        $sheets = [];

        $dataQuery->chunk($this->perPage, function ($data, $page) use (&$sheets) {
            $sheets[] = new DataSheets($this->exportable, $page, $data);
        });

        return $sheets;
    }

    public function properties(): array
    {
        return [
            'creator' => dbconfig('app_name'),
            'lastModifiedBy' => dbconfig('app_name'),
            'title' => $this->exportable['name'],
            'description' => $this->exportable['name'],
            'keywords' => $this->exportable['keywords'] ?? str('export,spreadsheet,')->append(dbconfig('app_name'))->append(',' . $this->exportable['name']),
            'category' => $this->exportable['name'],
            'company' => dbconfig('app_name'),
        ];
    }
}
