<?php

namespace App\Services;

use App\Exports\DataExports;
use App\Mail\ReportGenerated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * @template TModel
 */
class SimpleDataExporter
{
    /**
     * Default collection of email addresses to share exported items with
     *
     * @var \Illuminate\Support\Collection<int, \Illuminate\Support\Stringable>
     */
    protected \Illuminate\Support\Collection $data_emails;

    protected array $processing = [];

    protected array $valid_ids = [];

    /**
     *
     * @var \Illuminate\Support\Collection<int,array{id:string,model:class-string<TModel>,keywords:string,name:string,columns:array<int,string>}>
     */
    protected \Illuminate\Support\Collection $exportables;

    /**
     *
     * @param integer $perPage
     * @param array<int, string> $emails
     * @param array<int, string> $dataset
     */
    public function __construct(
        protected int $perPage = 50,
        protected array $emails = [],
        protected array $dataset = [],
    ) {
        $this->data_emails = collect($emails)->map(fn($e) => str($e));
        $this->exportables = collect(config('exports.set', []));
        $this->valid_ids = $this->exportables->pluck('id')->toArray();
        //dbconfig('notifiable_emails', collect([]))->map(fn($e) => str($e));
    }

    /**
     * Dispatch the exported data to the data_emails
     */
    private function dispatchMails(
        Model $dataset,
        string $title,
        int $batch = 1,
    ): void {
        $this->data_emails
            ->unique()
            ->filter(fn($e) => $e->isNotEmpty())
            ->each(function ($email) use ($dataset, $batch, $title) {
                RateLimiter::attempt(
                    'send-report:' . $email . $batch,
                    5,
                    fn() => Mail::to($email->toString())->send(new ReportGenerated($dataset, $batch, $title))
                );
            });
    }

    /**
     * Export data
     *
     * @param ?array<int,string> $types
     * @return void
     */
    private function exportData(?array $types = null, bool $noMails = false): void
    {
        /** @var array<int,array{model:class-string<TModel>,model_id:string|int|null,name:string,columns:array<int,string>}> $set */
        $set = $this->exportables->when($types, fn($sets) => $sets->filter(fn($s) => in_array($s['id'], $types)));

        foreach ($set as $exportable) {
            if ($exportable['model']::count() > 0) {
                echo  $path = "exports/{$exportable['id']}-dataset/data-export.xlsx";

                (new DataExports($exportable, $this->perPage))->store($path);

                if (!$noMails) {
                    $this->dispatchMails(
                        new $exportable['model'](),
                        $exportable['name'],
                        0
                    );
                }
            }
        }
    }

    /**
     * Export data
     *
     * @param Model $model
     * @return self
     */
    public function exportModel(Model $model)
    {
        /** @var array<int,array{model:class-string<TModel>,model_id:string|int|null,name:string,columns:array<int,string>}> $set */

        $name = str($model->getMorphClass())->afterLast('\\')->toString();

        $this->exportables = collect([[
            'id' => strtolower($name) . '-' . $model->id,
            'model' => $model->getMorphClass(),
            'model_id' => $model->id,
            'name' => $name . ' Data',
            'keywords' => 'data,exports,laravel op, ' . strtolower($name) . ' data',
            'columns' => $model->getFillable()
        ]]);
        return $this;
    }

    /**
     * Perform the actual export
     *
     * @param ?array<int,string> $types
     * @return void
     */
    public function export(?array $types = null, bool $noMails = false): void
    {
        if ($types) {
            $this->processing = array_merge($this->processing, $types);
            $this->exportData($types, $noMails);
        }
    }

    public function __destruct()
    {
        if (!empty($this->dataset)) {
            $validDataset = [];
            foreach ($this->dataset as $dataset) {
                $valids = join(',', $this->valid_ids);

                if (!in_array($dataset, $this->valid_ids)) {
                    throw new \Exception(
                        "$dataset is not a valid dataset, only \"$valids\" are allowed, check you exports.set config",
                        1
                    );
                }

                $validDataset[] = $dataset;
            }

            if (empty($this->processing)) {
                $this->exportData($validDataset);
            }

            return;
        }

        if (empty($this->processing)) {
            $this->exportData();
        }
    }
}
