<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ReviewCollection;
use App\Http\Resources\v1\ReviewResource;
use App\Http\Resources\v1\User\RelatedUserCollection;
use App\Http\Resources\v1\User\UserCollection;
use App\Http\Resources\v1\User\UserResource;
use App\Models\v1\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search and filter columns
        if ($request->search && ! $request->filters) {
            $query = User::search($request->search);
        }

        if ($request->filters) {
            $query->filtered($request->filters);
        }
        // $query->where('hidden', false);

        if (! $request->search) {
            if ($request->role) {
                $query->where('role', $request->role);
            }

            if ($request->type) {
                $query->where('type', $request->type);
            }

            // Reorder Columns
            if ($request->order === 'random') {
                $query->inRandomOrder();
            } elseif ($request->order === 'latest') {
                $query->latest();
            } elseif ($request->order === 'oldest') {
                $query->oldest();
            } elseif ($request->order && is_array($request->order)) {
                foreach ($request->order as $key => $dir) {
                    if ($dir == 'desc') {
                        $query->orderByDesc($key ?? 'id');
                    } else {
                        $query->orderBy($key ?? 'id');
                    }
                }
            }
        }

        $users = $query->paginate($request->get('limit', 15))->onEachSide(0)->withQueryString();

        return (new UserCollection($users))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\v1\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reviews(User $user)
    {
        return (new ReviewCollection($user->reviews()->with('user')->paginate()))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'image' => $user->image,
            ],
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    public function doFollow(User $user, $action = 'follow')
    {
        if ($action == 'toggle') {
            Auth()->user()->toggleFollow($user);
            $action = 'follow';
        } elseif ($action == 'follow') {
            Auth()->user()->follow($user);
        } else {
            Auth()->user()->unfollow($user);
        }

        // Send notification to user
        if ($action == 'follow') {
            $user->notify(new \App\Notifications\NewFollower(Auth()->user()));
        }

        return (new UserResource($user))->additional([
            'message' => __('You have :action :user', [
                'action' => Auth()->user()->isFollowing($user) ? 'followed' : 'unfollowed',
                'user' => $user->fullname,
            ]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Display a listing of the user relationships.
     *
     * @return \Illuminate\Http\Response
     */
    public function relationships(Request $request, User $user, $relationship = 'followers')
    {
        if ($relationship == 'followers') {
            $query = $user->followers();
        } else {
            $query = $user->followings();
        }

        // Search and filter columns
        if ($request->search) {
            $query = $user->search($request->search);
        }

        if (! $request->search) {
            if ($request->role) {
                $query->whereHasMorph('followable', User::class, function (Builder $query) use ($request) {
                    $query->where('role', $request->role);
                });
            }

            if ($request->type) {
                $query->whereHasMorph('followable', User::class, function (Builder $query) use ($request) {
                    $query->where('type', $request->type);
                });
            }

            // Reorder Columns
            if ($request->order === 'latest') {
                $query->latest();
            } elseif ($request->order === 'oldest') {
                $query->oldest();
            } elseif ($request->order && is_array($request->order)) {
                foreach ($request->order as $key => $dir) {
                    if ($dir == 'desc') {
                        $query->orderByDesc($key ?? 'id');
                    } else {
                        $query->orderBy($key ?? 'id');
                    }
                }
            }
        }

        $users = $query->paginate($request->get('limit', 15))->onEachSide(0)->withQueryString();

        return (new RelatedUserCollection($users))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function writeReview(Request $request, User $user)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $reviews = $user->reviews();

        // Relationship is the $order class and order id concatenated
        $review = $reviews->create([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'user_id' => Auth()->id(),
        ]);

        return (new ReviewResource($review))->additional([
            'message' => __('Your review has been submitted successfully.'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'image' => $user->image,
            ],
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v1\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return (new UserResource($user))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
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
