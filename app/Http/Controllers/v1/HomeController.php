<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Home\HomepageCollection;
use App\Http\Resources\v1\Home\HomepageResource;
use App\Http\Resources\v1\Home\NavigationCollection;
use App\Models\v1\Configuration;
use App\Models\v1\Home\Homepage;
use App\Models\v1\Home\HomepageContent;
use App\Models\v1\Home\Navigation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Homepage::query();

        // Search and filter columns
        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->whereFullText('meta', $request->search);
                $query->orWhere('title', $request->search);
                $query->orWhereHas('content', function ($q) use ($request) {
                    $q->whereFullText('content', $request->search);
                });
            });
        }

        // Reorder Columns
        if ($request->order && is_array($request->order)) {
            foreach ($request->order as $key => $dir) {
                if ($dir == 'desc') {
                    $query->orderByDesc($key ?? 'id');
                } else {
                    $query->orderBy($key ?? 'id');
                }
            }
        }

        $pages = $query->paginate();

        return (new HomepageCollection($pages))->response()->setStatusCode(HttpStatus::OK);
    }

    public function page($page = null)
    {
        if (isset($page)) {
            $page = Homepage::whereId($page)->orWhere('slug', $page)->firstOrFail();
        } else {
            $page = Homepage::whereDefault(true)->firstOrFail();
        }

        return (new HomepageResource($page))->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display a listing of the navigations resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function navigations(Request $request)
    {
        $query = Navigation::active();

        if ($request->group && $request->group !== 'all') {
            $query->byGroup($request->group);
        }

        if ($request->location) {
            $query->byLocation($request->location);
        }

        // Reorder Columns
        if ($request->order && $request->order === 'latest') {
            $query->latest();
        } elseif ($request->order && $request->order === 'oldest') {
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

        if ($request->group === 'all') {
            // Split the collection into groups by location the split the groups by group and return the collection
            $navigations = $query->get()->groupBy('location')->map(function ($item, $key) {
                return $item->groupBy('group')->map(function ($item, $key) {
                    return new NavigationCollection($item);
                });
            });

            return $this->buildResponse([
                'message' => HttpStatus::message(HttpStatus::OK),
                'status' => 'success',
                'status_code' => HttpStatus::OK,
                'data' => $navigations,
            ]);
        }

        if ($request->important) {
            $query->important();
        }

        $navs = $query->paginate(15)->onEachSide(1)->withQueryString();

        return (new NavigationCollection($navs))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request)
    {
        $loadAll = $request->load ?? false;
        $home_content = HomepageContent::where('linked', true)
                        ->where('parent', function ($query) {
                            $query->select('id')->from('homepages')->where('default', true);
                        })
                        ->where('slug', '!=', null)
                        ->where('slug', '!=', '')
                        ->get(['id', 'title', 'slug']);


        return $this->buildResponse([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'settings' => collect(config('settings'))
                ->except(['permissions', 'messages', 'system'])
                ->filter(fn ($v, $k) => stripos($k, 'secret') === false)
                ->mergeRecursive([
                    'oauth' => [
                        'google' => collect(config('services.google'))->filter(fn ($v, $k) => stripos($k, 'secret') === false),
                        'facebook' => collect(config('services.facebook'))->filter(fn ($v, $k) => stripos($k, 'secret') === false),
                    ],
                ]),
            'website' => [
                'content' => $home_content,
                'links' => Homepage::where('default', false)->orderBy('priority')->get()->mapWithKeys(function ($value, $key) {
                    return [$key => [
                        'id' => $value->id,
                        'slug' => $value->slug,
                        'title' => $value->title,
                    ]];
                }),
            ],
            'configurations' => (new Configuration)->build($loadAll),
            'csrf_token' => csrf_token(),
        ]);
    }
}