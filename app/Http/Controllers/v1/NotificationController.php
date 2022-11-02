<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\NotificationCollection;
use App\Http\Resources\v1\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('unread')) {
            $notification = auth()->user()->unreadNotifications();
        } else {
            $notification = auth()->user()->notifications();
        }

        return $this->load($request, $notification);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function load(Request $request, $notification = null)
    {
        if ($request->has('unread')) {
            $query = $notification ?? auth()->user()->unreadNotifications();
        } else {
            $query = $notification ?? auth()->user()->notifications();
        }

        $notifications = $query
            ->paginate($request->get('limit', 15))
            ->withQueryString();

        // Push new notifications if total filtered is less than request limit
        $notifications = $this->padNotifications($request, $notifications, $notification);

        // Push new notifications if total filtered is less than request limit
        $notifications = $this->padNotifications($request, $notifications, $notification);

        return (new NotificationCollection($notifications))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    protected function padNotifications(Request $request, $notifications, $notification)
    {
        // Push new notifications if total filtered is less than request limit
        if ($notifications->count() > 0 && $notifications->count() < $request->get('limit', 15)) {
            if ($request->has('unread')) {
                $newQuery = ($notification ?? auth()->user()->unreadNotifications())
                    ->where('created_at', '>', $notifications->last()->created_at);
            } else {
                $newQuery = ($notification ?? auth()->user()->notifications())
                    ->where('created_at', '>', $notifications->last()->created_at);
            }
            $newNotifications = $newQuery->paginate($request->get('limit', 15) - $notifications->count())
                ->withQueryString();
            $notifications = $notifications->merge($newNotifications);
        }

        return $notifications;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->unreadNotifications();

        $notification->markAsRead();

        return (new NotificationResource($notification))->additional([
            'message' => 'Marked as read',
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
