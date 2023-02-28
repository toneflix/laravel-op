<?php

namespace App\Http\Controllers\v1\User;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\GenericRequestCollection;
use App\Http\Resources\v1\User\GenericRequestResource;
use App\Models\v1\GenericRequest;
use App\Models\v1\User;
use App\Traits\Meta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenericRequestController extends Controller
{
    use Meta;

    protected $map_types = [
        'book_session' => 'book a session',
    ];

    protected $map_models = [
    ];

    protected $map_mapends = [
        'book_session' => 'session booking',
    ];

    protected $finalModels = [
        'book_session' => 'App\Models\v1\Event',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 15);

        $query = GenericRequest::query();

        if ($request->has('accepted')) {
            $query->accepted();
        }

        if ($request->has('rejected')) {
            $query->rejected();
        }

        if ($request->has('pending')) {
            $query->pending();
        }

        if ($request->has('direction') && $request->get('direction') === 'outgoing') {
            $query->outgoing();
        } elseif ($request->has('direction') && $request->get('direction') === 'incoming') {
            $query->incoming();
        } else {
            $query->own();
        }

        $requests = $query->latest()->paginate($limit)->withQueryString();

        return (new GenericRequestCollection($requests))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Create a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $notify_user = true;
        $success_msg = 'We have sent a :1 request to :0, we will let you know when they respond';

        $request->validate([
            'type' => 'required|string',
            'date' => 'required_if:type,==,book_session|string',
            'user_id' => 'required|numeric',
        ]);

        $user = User::find($request->user_id);
        if (! $user) {
            return $this->buildResponse([
                'message' => 'User not found.',
                'status' => 'error',
                'status_code' => HttpStatus::NOT_FOUND,
            ]);
        }

        if (GenericRequest::where('meta->type', $request->type)
            ->whereModel($this->finalModels[$request->get('type')] ?? 'App\Models\v1\Event')
            ->whereUserId($user->id)
            ->outgoing()->pending()->exists()
        ) {
            return $this->buildResponse([
                'message' => 'You already have a pending request of this type',
                'status' => 'error',
                'status_code' => HttpStatus::TOO_MANY_REQUESTS,
            ]);
        }

        $meta = [
            'type' => $request->get('type'),
            'meta' => $request->get('meta'),
            'title' => __('New :0 request', [$map_types[$request->type] ?? $request->type]),
        ];

        if ($request->type === 'book_session') {
            $start = Carbon::parse($request->input('date', now()));
            $end = Carbon::parse($request->input('date', now()))->addMinutes(5)->subSecond();

            $meta = [
                ...$meta,
                'user_id' => $request->user()->id,
                'title' => __(':0 wants to :1 with you', [$request->user()->fullname, $map_types[$request->type] ?? $request->type]),
                'details' => __(':0 is requesting to book a session with you for :1 at :2', [
                    $request->user()->fullname,
                    str($request->date)->before(' '),
                    str($request->date)->after(' '),
                ]),
                'start_date' => $start,
                'end_date' => $end,
                'duration' => $start->diffInMinutes($end),
                'color' => $request->input('color', '#480d19'),
                'bgcolor' => $request->input('bgcolor', '#e8e7e7'),
                'border_color' => $request->input('border_color', '#480d19'),
                'location' => '',
                'notify' => true,
            ];
        }

        $generic = GenericRequest::create([
            'user_id' => $request->user_id,
            'sender_id' => Auth::id(),
            'message' => $meta['title'] ?? '',
            'model' => $this->finalModels[$request->get('type')] ?? 'App\Models\v1\Event',
            'meta' => $meta,
        ]);

        if (config('settings.auto_booking', true) && $request->type === 'book_session') {
            $generic->update(['accepted' => true, 'rejected' => false]);
            $this->genericAction($generic, 'accepted', false);
            $notify_user = false;
            $success_msg = 'We have booked a session with :0 for you, you can find it in your event manager';
        }

        if ($generic->model === 'App\Models\v1\Event' && $notify_user) {
            // Send notification to the user
            $generic->user->notify(new \App\Notifications\GenericRequest($generic));
        }

        return (new GenericRequestResource($generic))->additional([
            'message' => __($success_msg, [
                $generic->user->fullname,
                $this->map_mapends[$request->type] ?? $request->type,
            ]),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request = GenericRequest::own()->findOrFail($id);

        return (new GenericRequestResource($request))->additional([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:accepted,rejected',
        ]);

        $gen = GenericRequest::incoming()->findOrFail($id);

        $gen->update([
            'accepted' => $request->get('status') === 'accepted',
            'rejected' => $request->get('status') === 'rejected',
        ]);

        // $generic_item = [];
        // $generic_type = 'default';

        [$generic_type, $generic_item] = $this->genericAction($gen, $request->status, true);

        return (new GenericRequestResource($gen))->additional([
            'item' => $generic_item,
            'type' => $generic_type,
            'message' => __('Request :0 successfully', [$request->get('status')]),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $request = GenericRequest::outgoing()->findOrFail($id);

        $request->delete();

        return $this->buildResponse([
            'message' => __('Request deleted successfully'),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Generic action
     *
     * @param  GenericRequest  $gen
     * @param  string  $status
     * @param  bool  $notify_user
     * @return array
     */
    protected function genericAction(GenericRequest $request, string $status, bool $notify_user = true): array
    {
        $request->notification && $request->notification->update(['read_at' => now(), 'data->has_action' => false]);

        $generic_type = 'default';
        $generic_item = [];

        if ($request->model === 'App\Models\v1\Event') {
            $generic_item = $request->user->events()->create(
                collect($request->meta)->merge([
                    'creator_id' => $request->sender->id,
                    'type' => 'book_session',
                ])->toArray()
            );

            $generic_type = 'event';

            if ($notify_user) {
                // Send notification to the user
                $request->sender->notify(new \App\Notifications\GenericRequest($request, $status));
            } else {
                $request->user->notify(new \App\Notifications\GenericRequest($request, $status, [
                    'message' => $generic_item->details ?? '',
                    'type' => 'book_session',
                    'has_action' => false,
                    'request' => [],
                ]));
                $request->delete();
            }
        }

        return [$generic_type, $generic_item];
    }
}
