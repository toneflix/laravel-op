<?php

namespace App\Exports;

use App\Exports\Sheets\DataSheets;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Excel;

/**
 * @template TModel
 */
class DataExports implements Export, WithMultipleSheets, WithProperties
{
    use Exportable;

    /**
     * @param array{id:string,model:class-string<TModel>,model_id:string|int|null,keywords:string,name:string,columns:array<int,string>} $exportable
     * @param int $perPage
     *
     */
    public function __construct(
        protected array $exportable,
        protected int $perPage = 50,
    ) {
        // These properties are declared by the Exportable trait itself (with
        // native types), so they're set here rather than redeclared, which
        // would otherwise be an incompatible property definition.
        $this->writerType = Excel::XLSX;
        $this->disk = 'protected';
        $this->diskOptions = [
            'visibility' => 'public',
        ];
    }

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
