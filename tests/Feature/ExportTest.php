<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SimpleDataExporter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class ExportTest extends TestCase
{
    public function test_can_export_model_data(): void
    {
        Mail::fake();

        $users = User::factory(3)->create();

        (new SimpleDataExporter(50, ['user@example.com']))->export();

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $file = collect(Storage::disk('protected')->allFiles())->last();

        $spreadsheet = $reader->setReadDataOnly(true)->load(Storage::disk('protected')->path($file));


        $this->assertSame(
            $users[0]->firstname,
            $spreadsheet->getAllSheets()[0]->getCell([1, 3])->getValue()
        );

        Storage::disk('protected')->delete($file);
    }

    public function test_can_mail_exported_model_data(): void
    {
        Mail::fake();

        User::factory(3)->create();

        (new SimpleDataExporter(50, ['user@example.com']))->export();

        $file = collect(Storage::disk('protected')->allFiles())->last();

        Mail::assertSent(\App\Mail\ReportGenerated::class, 'user@example.com');

        Storage::disk('protected')->delete($file);
    }
}
