<?php

namespace App\Exports\Sheets;

use App\Helpers\Transformer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @template TModel
 */
class DataSheets implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    /**
     * The sheets constructor
     *
     * @param array{id:string,model:class-string<TModel>,model_id:string|int|null,keywords:string,name:string,columns:array<int,string>} $exportable
     * @param int $page
     * @param  \Illuminate\Database\Eloquent\Collection<int, TModel>  $submisions
     */
    public function __construct(
        protected array $exportable,
        protected int $page,
        protected \Illuminate\Database\Eloquent\Collection $submisions
    ) {}

    public function headings(): array
    {
        $submision = $this->submisions->first();

        return collect(array_keys($this->map($submision)))->map(
            fn($e) => str($e)
                ->replace('_', ' ')
                ->title()
                ->replace(['Id'], ['ID'])
                ->toString()
        )->toArray();
    }

    public function collection()
    {
        return $this->submisions;
    }

    /**
     * Undocumented function
     *
     * @param  TModel  $submision
     */
    public function map($submision): array
    {
        $data = collect($this->exportable['columns'])->mapWithKeys(function ($key) use ($submision) {
            $value = $submision[$key] ?? '';

            $valueKey = match ($key) {
                'created_at' => 'Join Date',
                default => (string) $key
            };

            if (config("exports.transformers.$key.0") && method_exists(Transformer::class, config("exports.transformers.$key.0"))) {
                $transformed = call_user_func_array(
                    array(Transformer::class, config("exports.transformers.$key.0")),
                    [$value, ...config("exports.transformers.$key.1")]
                );
                return [$valueKey => $transformed];
            }

            return [$valueKey => (string) $value];
        });

        // $data->prepend($submision->id, '#');

        return $data->toArray();
    }

    public function title(): string
    {
        return 'Page ' . $this->page;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            'A' => ['font' => ['bold' => true]],
            'B' => ['font' => ['bold' => true]],
        ];
    }
}
