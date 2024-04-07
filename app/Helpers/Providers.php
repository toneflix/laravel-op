<?php

namespace App\Helpers;

use App\Enums\HttpStatus;
use App\Models\Configuration;
use App\Models\PasswordCodeResets;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;

class Providers
{
    /**
     * Build response
     *
     * @var array<int, string>
     *
     * @return Collection|string|int|float|array
     */
    protected static $responseKeys = [];

    public static function config(
        $key = null,
        $default = null,
        $loadSecret = false
    ): Collection|string|int|float|array|null {
        $config = Configuration::build($loadSecret);

        if (is_null($key)) {
            return $config;
        }

        return Arr::get($config, $key, $default);
    }

    /**
     * Build response
     *
     * @param  array<int, string>  $only
     * @return ResponseInterface
     */
    public static function response($only = [])
    {
        if (is_array($only) && count($only)) {
            static::$responseKeys = $only;
        }

        return response();
    }

    /**
     * Create response macros.
     */
    public static function buildResponseMacros(): void
    {
        // Create a successfull response
        Response::macro(
            'success',
            function (array|Collection|AbstractPaginator|JsonResource $data, int|HttpStatus $code = 200, ?array $extra = []) {
                return Providers::jsonResource($data, 'success', $code, $extra);
            }
        );

        // Create an error response
        Response::macro(
            'error',
            function (array|Collection|AbstractPaginator|JsonResource $data, int|HttpStatus $code = 400, ?array $extra = []) {
                return Providers::jsonResource($data, 'error', $code, $extra);
            }
        );

        // Create an info response
        Response::macro(
            'info',
            function (array|Collection|AbstractPaginator|JsonResource $data, int|HttpStatus $code = 200, ?array $extra = []) {
                return Providers::jsonResource($data, 'info', $code, $extra);
            }
        );
    }

    /**
     * Prepare the response for the buildResponseMacros().
     */
    public static function jsonResource(
        array|Collection|AbstractPaginator|JsonResource $data,
        string $type,
        int|HttpStatus $code = 200,
        array|Collection $extra = []
    ): JsonResponse|\Illuminate\Http\Response {
        $status = $code instanceof HttpStatus ? $code->value : $code;

        // If the data is an instance of JsonResource we will return it instead.
        if ($data instanceof JsonResource) {
            return $data->additional([
                ...$extra,
                'status_code' => $status,
                'message' => $extra['message'] ?? $data['message'] ?? HttpStatus::from($status)->name,
                'status' => $type,
            ])->response()->setStatusCode($status);
        }

        if ($data instanceof AbstractPaginator) {
            $data = self::paginator($data);
        }

        // Return  the data wrapped in an "information" array and set the status to informational.
        return Response::make([
            ...$data,
            'status_code' => $status,
            'message' => $extra['message'] ?? $data['message'] ?? HttpStatus::from($status)->name,
            'status' => $type,
        ], $status);
    }

    /**
     * Add Rate limit for code requests.
     */
    public static function rateLimitCodeRequests(): void
    {
        RateLimiter::for('code-requests', function (Request $request) {
            if (collect(['verification.send', 'verification.store'])->contains($request->route()->getName())) {
                $check = $request->user();

                $action = 'activate your account';

                /** @var \Carbon\Carbon */
                $datetime = $check->last_attempt;
            } else {
                $check = PasswordCodeResets::whereEmail($request?->email)->first();

                $action = 'reset your password';

                /** @var \Carbon\Carbon */
                $datetime = $check->created_at ?? null;
            }

            $dateAdd = $datetime?->addSeconds(config('settings.token_lifespan', 30));

            return (! $datetime || $dateAdd->isPast())
                ? Limit::none()
                : response()->info([
                    'message' => __("We already sent a message to help you {$action}, you can try again :0.", [
                        $dateAdd->diffForHumans(),
                    ]),
                    'time_left' => $dateAdd->shortAbsoluteDiffForHumans(),
                    'try_at' => $dateAdd->toDateTimeLocalString(),
                ], HttpStatus::TOO_MANY_REQUESTS);
        });
    }

    /**
     * Build the data paginator
     */
    public static function paginator(LengthAwarePaginator $data): array
    {
        if ($data instanceof LengthAwarePaginator) {
            $links = $data->linkCollection()->filter(fn ($link) => is_numeric($link['label']));

            return [
                'data' => count(static::$responseKeys)
                    ? collect($data->items())
                        ->map(fn ($e) => collect($e)->filter(fn ($k, $v) => in_array($v, static::$responseKeys)))
                    : $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'from' => $data->firstItem(),
                    'last_page' => $data->lastPage(),
                    'links' => $data->linkCollection(),
                    'path' => $data->path(),
                    'per_page' => $data->perPage(),
                    'to' => $data->lastItem(),
                    'total' => $data->total(),
                ],
                'links' => [
                    'first' => $links->first()['url'] ?? null,
                    'last' => $links->last()['url'] ?? null,
                    'prev' => $data->previousPageUrl(),
                    'next' => $data->nextPageUrl(),
                ],
            ];
        }

        return [];
    }

    public static function getFees(int|float|string $fee, int|float $amount): int|float
    {
        if (is_string($fee)) {
            $parsed = (float) filter_var(
                $fee,
                FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND
            );

            if (str_contains($fee, '%')) {
                $fee = (float) $parsed * $amount / 100;
            } else {
                $fee = $parsed;
            }
        }

        return abs($fee);
    }

    /**
     * @param  int|string  $number  The input number to shorten
     * @return string A string representing the reformatted and shortened number
     */
    public static function money($number, $abbrev = false)
    {
        return static::config('currency_symbol') . (
            $abbrev === false
            ? number_format($number, 2)
            : static::numberAbbr($number)
        );
    }

    /**
     * Converts a number into a short version, eg: 1000 -> 1k
     * Based on: http://stackoverflow.com/a/4371114
     *
     * @author Nivesh Saharan https://stackoverflow.com/users/5083810/nivesh-saharan
     * @author 3m1n3nc3 https://stackoverflow.com/users/10685553/3m1n3n3
     *
     * @param  int|string  $n  The input number to shorten
     * @return string A string representing the reformatted and shortened number
     */
    public static function numberAbbr($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } elseif ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } elseif ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } elseif ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }

        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }

        return $n_format . $suffix;
    }

    /**
     * Revenue Conversion
     *
     * Revenue pool = 50% of Total income (App Stream Income)
     *
     * ORIGINAL FORMULAR (Doesn's seem to work)
     * User Sreams / Total streams x Revenue Pool
     * ===========================================================
     *
     * LEGACY'S TOUCH
     * (Track Streams * Stream Worth) / (1 + Revenue Pool Percentage)
     * E.g: (100 * 1) / (1 + 50%)
     */
    public static function revenue(int|float $totalStreams): int|float
    {
        $streamWorth = static::config('stream_worth', 1);
        $revenuePoolPercentage = (float) static::config('revenue_pool_percentage', 50);
        if ($revenuePoolPercentage > 1) {
            $revenuePoolPercentage /= 100;
        }

        // Ensure revenue pool percentage is between 0 and 1
        $revenuePoolPercentage = min(max($revenuePoolPercentage, 0), 1);

        // Calculate total stream value
        $totalStreamValue = $totalStreams * $streamWorth;

        // Calculate creator earnings
        $creatorEarnings = $totalStreamValue / (1 + $revenuePoolPercentage);

        return round($creatorEarnings, 2);
    }
}
