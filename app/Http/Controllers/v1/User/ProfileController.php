<?php

namespace App\Http\Controllers\v1\User;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\RelatedUserCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function relationships(Request $request, $relationship = 'followers')
    {
        $user = Auth::user();

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
                $query->where('role', $request->role);
            }

            if ($request->type) {
                $query->where('type', $request->type);
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
}
