<?php

namespace App\Http\Controllers\v1\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SubscriptionCollection;
use App\Http\Resources\v1\TransactionResource;
use App\Models\v1\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $status = null)
    {
        $this->authorize('can-do', ['subscriptions']);
        $limit = $request->get('limit', 30);
        $query = Subscription::orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        $subscriptions = $query->paginate($limit);

        return (new SubscriptionCollection($subscriptions))->additional([
            'message' => 'OK',
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
    public function show(Subscription $subscription)
    {
        $this->authorize('can-do', ['subscriptions']);

        return (new TransactionResource($subscription))->additional([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {
        $this->authorize('can-do', ['subscriptions']);
        if ($request->items) {
            $count = collect($request->items)->map(function ($item) use ($request) {
                $item = Subscription::find($item);
                if ($item) {
                    $delete = $item->delete();

                    return count($request->items) === 1 ? $item->user->fullname : $delete;
                }

                return false;
            })->filter(fn ($i) => $i !== false);

            return $this->buildResponse([
                'message' => $count->count() === 1
                    ? __(":0's subscription has been deleted", [$count->first()])
                    : __(":0 subscriptions have been deleted.", [$count->count()]),
                'status' => 'success',
                'status_code' => HttpStatus::ACCEPTED,
            ]);
        } else {
            $item = Subscription::findOrFail($id);
            $item->delete();

            return $this->buildResponse([
                'message' => __(":0's subscription has been deleted.", [$item->user->fullname]),
                'status' => 'success',
                'status_code' => HttpStatus::ACCEPTED,
            ]);
        }
    }
}