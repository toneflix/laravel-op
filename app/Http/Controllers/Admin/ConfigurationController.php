<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HttpStatus;
use App\Helpers\Provider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ToneflixCode\DbConfig\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Provider::response()->success([
            'data' => Configuration::notSecret()->get()
                ->when($request->boolean('group'), fn($model) => $model->groupBy('group')),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id)
    {
        $data = Configuration::where('key', $id)
            ->when(filter_var($id, FILTER_VALIDATE_INT), fn($q) => $q->orWhere('id', $id))
            ->first();

        return Provider::response()->success([
            'data' => $data,
        ]);
    }

    /**
     * Save the configuration.
     */
    public function store(Request $request)
    {
        $rules = Configuration::all()->mapWithKeys(function ($conf) {
            return [$conf->key => ['nullable', $conf->type]];
        })->toArray();

        $valid = $this->validate($request, $rules);

        $config = Provider::config($valid);

        return Provider::response()->success([
            'data' => Configuration::notSecret()->orderBy('id')->get()
                ->when($request->boolean('group'), fn($model) => $model->groupBy('group')),
            'config' => $config,
            'message' => 'Configuration Saved!',
        ], HttpStatus::ACCEPTED);
    }
}
