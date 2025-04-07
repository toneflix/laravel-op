<?php

namespace App\Services;

use App\Exports\DataExports;
use App\Jobs\DispatchExportsNotifications;
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
     * @var \Illuminate\Support\Collection<int,array{id:string,model:class-string<TModel>,keywords:string,name:string,columns:array<int,string>}>
     */
    protected \Illuminate\Support\Collection $exportables;

    /**
     * @param  int  $perPage
     * @param  array<int, string>  $emails
     * @param  array<int, string>  $dataset
     * @param  bool  $queue
     */
    public function __construct(
        protected int $perPage = 50,
        protected array $emails = [],
        protected array $dataset = [],
        protected bool $queue = false,
    ) {
        $this->data_emails = collect($emails)->map(fn($e) => str($e));
        $this->exportables = collect(config('exports.set', []));
        $this->valid_ids = $this->exportables->pluck('id')->toArray();
        //dbconfig('notifiable_emails', collect([]))->map(fn($e) => str($e));
    }

    /**
     * Export data
     *
     * @param  ?array<int,string>  $types
     */
    private function exportData(?array $types = null, bool $noMails = false): void
    {
        /** @var array<int,array{model:class-string<TModel>,model_id:string|int|null,name:string,columns:array<int,string>}> $set */
        $set = $this->exportables->when($types, fn($sets) => $sets->filter(fn($s) => in_array($s['id'], $types)));

        foreach ($set as $exportable) {
            if ($exportable['model']::count() > 0) {
                $path = "exports/{$exportable['id']}-dataset/data-export.xlsx";

                if ($this->queue) {
                    (new DataExports($exportable, $this->perPage))->queue($path)->chain(! $noMails ? [
                        new DispatchExportsNotifications(
                            $exportable['model']::first(),
                            $this->data_emails,
                            $exportable['name'],
                            0
                        )
                    ] : []);
                } else {
                    (new DataExports($exportable, $this->perPage))->store($path);

                    if (! $noMails) {
                        DispatchExportsNotifications::dispatch(
                            $exportable['model']::first(),
                            $this->data_emails,
                            $exportable['name'],
                            0
                        );
                    }
                }
            }
        }
    }

    /**
     * Export data
     *
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
            'columns' => $model->getFillable(),
        ]]);

        return $this;
    }

    /**
     * Perform the actual export
     *
     * @param  ?array<int,string>  $types
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
        if (! empty($this->dataset)) {
            $validDataset = [];
            foreach ($this->dataset as $dataset) {
                $valids = implode(',', $this->valid_ids);

                if (! in_array($dataset, $this->valid_ids)) {
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
