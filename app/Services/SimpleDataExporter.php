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

    public function __construct(
        protected int $perPage = 50,
        protected array $emails = [],
    ) {
        $this->data_emails = collect($emails)->map(fn($e) => str($e)); //dbconfig('notifiable_emails', collect([]))->map(fn($e) => str($e));
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

    private function exportData(): void
    {
        /** @var array<int,array{model:class-string<TModel>,name:string,columns:array<int,string>}> $set */
        $set = config('exports.set', []);

        foreach ($set as $exportable) {
            if ($exportable['model']::count() > 0) {
                $path = 'exports/users-dataset/data-batch-0.xlsx';
                (new DataExports($exportable, $this->perPage))->store($path);

                $this->dispatchMails(
                    new $exportable['model'](),
                    $exportable['name'],
                    0
                );
            }
        }
    }

    /**
     * Perform the actual export
     *
     * @param ?string $type
     * @return void
     */
    public function export(?string $type = null): void
    {
        if ($type) {
            $this->processing = array_merge($this->processing, [$type]);
            $this->exportData($type);
        }
    }

    public function __destruct()
    {
        if (empty($this->processing)) {
            $this->exportData();
        }
    }
}
