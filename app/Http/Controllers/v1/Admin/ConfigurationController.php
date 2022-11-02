<?php

namespace App\Http\Controllers\v1\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\v1\Configuration;
// use App\Models\v1\File;
// use App\Models\v1\Transaction;
// use App\Models\v1\User;
use App\Services\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
// use Flowframe\Trend\Trend;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function saveSettings(Request $request)
    {
        \Gate::authorize('can-do', ['configuration']);

        if ($request->has('type') && $request->type == 'configuration') {
            return $this->saveConfiguration($request);
        }

        $this->validate($request, [
            'contact_address' => ['nullable', 'string'],
            'currency' => ['required', 'string'],
            'currency_symbol' => ['nullable', 'string'],
            'default_banner' => [Rule::requiredIf(fn () => ! config('settings.default_banner')), 'mimes:jpg,png'],
            'auth_banner' => [Rule::requiredIf(fn () => ! config('settings.auth_banner')), 'mimes:jpg,png'],
            'welcome_banner' => [Rule::requiredIf(fn () => ! config('settings.welcome_banner')), 'mimes:jpg,png'],
            'frontend_link' => ['nullable', 'string'],
            'prefered_notification_channels' => ['required', 'array'],
            'keep_successful_queue_logs' => ['nullable'],
            'site_name' => ['required', 'string'],
            'slack_debug' => ['nullable', 'boolean'],
            'slack_logger' => ['nullable', 'boolean'],
            'token_lifespan' => ['required', 'numeric'],
            'trx_prefix' => ['required', 'string'],
            'verify_email' => ['nullable', 'boolean'],
            'verify_phone' => ['nullable', 'boolean'],
        ]);

        collect($request->all())->except(['_method'])->map(function ($config, $key) use ($request) {
            $key = str($key)->replace('__', '.')->__toString();
            if ($request->hasFile($key)) {
                (new Media)->delete('default', pathinfo(config('settings.'.$key), PATHINFO_BASENAME));
                $save_name = (new Media)->save('default', $key, $config);
                $config = (new Media)->image('default', $save_name, asset('media/default.jpg'));
            } elseif (($type = collect($this->data_type))->has($key)) {
                if (! is_array($config) && $type->get($key) === 'array') {
                    $config = valid_json($config, true, explode(',', $config));
                } elseif ($type->get($key) === 'boolean') {
                    $config = boolval($config);
                } elseif ($type->get($key) === 'number') {
                    $config = intval($config);
                }
            }

            Config::write("settings.{$key}", $config);
        });

        $settings = collect(config('settings'))
            ->except(['permissions', 'messages', 'system'])
            ->filter(fn ($v, $k) => stripos($k, 'secret') === false)
            ->mergeRecursive([
                'oauth' => [
                    'google' => collect(config('services.google'))->filter(fn ($v, $k) => stripos($k, 'secret') === false),
                    'facebook' => collect(config('services.facebook'))->filter(fn ($v, $k) => stripos($k, 'secret') === false),
                ],
                'configurations' => (new Configuration())->build(),
            ]);

        return $this->buildResponse([
            'data' => collect($settings)->put('refresh', ['settings' => $settings]),
            'message' => 'Configuration Saved.',
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ]);
    }

    public function saveConfiguration(Request $request)
    {
        $configs = Configuration::all()->collect();
        $validations = $configs->mapWithKeys(function ($config) {
            $key = $config->key;
            $vals[] = 'nullable';
            if ($config->type === 'files') {
                $vals[] = 'file';
                $vals[] = 'mimes:jpg,png,jpeg,gif,mpeg,mp4,webm';
                $key = $key.'.*';
            } else {
                $vals[] = $config->type ?? 'string';
            }

            if ($config->count && $config->type !== 'files') {
                $vals[] = 'max:'.$config->count;
            } elseif ($config->max && $config->type === 'files') {
                $vals[] = 'max:'.$config->max;
            }

            return [$key => implode('|', $vals)];
        });

        $attrs = $validations->keys()->mapWithKeys(function ($key) use ($configs) {
            $key = str($key)->remove('.*', false)->toString();

            return [$key => $configs->where('key', $key)->first()->title];
        });

        $this->validate($request, $validations->toArray(), [], $attrs->toArray());

        $configs->each(function ($config) use ($request) {
            $key = $config->key;
            $value = $request->input($key);
            if ($config->type === 'files' && $request->hasFile($key) && is_array($request->file($key))) {
                // dd(collect($request->file($key))->keys(), $config->files->keys()->push(...collect($request->file($key))->keys()));
                // foreach ($request->file($key) as $index => $image) {
                // }
                foreach ($request->file($key) as $index => $image) {
                    if (isset($config->files[$index])) {
                        $config->files[$index]->delete();
                    }
                    $config->files()->save(new Image([
                        'file' => (new Media)->save('default', $key, null, $index),
                    ]));
                }
            } elseif ($config->type === 'file' && $request->hasFile($key)) {
                $config->files()->delete();
                $config->files()->save(new Image([
                    'file' => (new Media)->save('default', $key, $config->files[0]->file ?? null),
                ]));
            } else {
                $config->value = $value;
                $config->save();
            }
        });

        return $this->buildResponse([
            'data' => collect((new Configuration())->build())->put('refresh', ['configurations' => (new Configuration())->build()]),
            'message' => 'Configuration saved successfully',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadStats(Request $request)
    {
        //
    }
}