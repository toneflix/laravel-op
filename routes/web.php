<?php

use App\Enums\HttpStatus;
use App\Helpers\Url;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return [
        'api' => dbconfig('app_name'),
        'version' => VERSION
    ];
});

Route::get('download/formdata/{timestamp}/{form}/{batch?}', function ($timestamp, $data, int $batch = 0) {

    $setTime = Carbon::createFromTimestamp($timestamp);

    if ($setTime->diffInSeconds(now()) > 36000) {
        abort(HttpStatus::BAD_REQUEST->value, 'Link expired');
    }

    $storage = Storage::disk('protected');

    // public Form|Company|Appointment|User $form,
    $id = str(Url::base64urlDecode($data))->explode('/')->last();

    /** @var \App\Models\User $class */
    $model = app(str(str(Url::base64urlDecode($data))->explode('/')->first())->replace('.', '\\')->toString());

    // if ($model instanceof Form) {
    //     $form = $model->findOrFail($id);
    //     $groupName = 'forms-' . $form->id;
    // } else {
    $groupName = str(get_class($model))->afterLast('\\')->lower()->plural()->append('-dataset')->toString();
    // }

    // Sometimes, we migh get the wrong data batch, let's try the next 10 batches till we get it right
    $i = $batch + 10;
    do {
        $path = 'exports/' . $groupName . '/data-batch-' . $batch . '.xlsx';
        if ($storage->exists($path)) {
            $mime = $storage->mimeType($path);

            // create response and add encoded image data
            return Response::download($storage->path($path), $groupName . '-' . $setTime->format('Y-m-d H_i_s') . '.xlsx', [
                'Content-Type' => $mime,
                'Cross-Origin-Resource-Policy' => 'cross-origin',
                'Access-Control-Allow-Origin' => '*',
            ]);
            break;
        } else {
            $batch++;
            continue;
        }
    } while ($batch <= $i);

    abort(HttpStatus::NOT_FOUND->value, 'Link Does Not Exist');
})->middleware('auth.basic')->name('download.formdata');
