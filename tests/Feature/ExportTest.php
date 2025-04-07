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
        $users = User::factory(3)->create();

        (new SimpleDataExporter())->export();

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

    public function test_can_export_specific_model_data(): void
    {
        $users = User::factory(3)->create();

        (new SimpleDataExporter(dataset: ['users']))->export();

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $file = collect(Storage::disk('protected')->allFiles())->last();

        $spreadsheet = $reader->setReadDataOnly(true)->load(Storage::disk('protected')->path($file));

        $this->assertSame(
            $users[0]->firstname,
            $spreadsheet->getAllSheets()[0]->getCell([1, 3])->getValue()
        );

        $this->assertTrue(str($file)->after('exports/')->before('-dataset/')->is('users'));

        Storage::disk('protected')->delete($file);
    }

    public function test_can_export_a_particular_model(): void
    {
        $users = User::factory(3)->create();

        (new SimpleDataExporter(50))->exportModel($users[0])->export(noMails: true);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $file = collect(Storage::disk('protected')->allFiles())->last();

        $spreadsheet = $reader->setReadDataOnly(true)->load(Storage::disk('protected')->path($file));

        $this->assertSame(
            $users[0]->firstname,
            $spreadsheet->getAllSheets()[0]->getCell([1, 2])->getValue()
        );

        $this->assertTrue(str($file)->after('exports/')->before('-dataset/')->is('user-' . $users[0]->id));

        Storage::disk('protected')->delete($file);
    }
}
