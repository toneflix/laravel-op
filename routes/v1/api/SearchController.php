<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use Illuminate\Http\Request;
// use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q
        ? (new Search())
        ->registerModel(User::class, 'firstname', 'lastname', 'about', 'email')
        ->limitAspectResults($request->get('limit', 25))
        ->search($request->get('q', '')) : collect([]);

        $results = $search->map(function ($result) {
            $item = $result->searchable;

            return [
                'id' => $result->searchable->id,
                'key' => str($result->searchable->id)->append($result->type)->slug(),
                'url' => $result->url,
                'url' => ['slug' => $item->slug],
                'title' => $result->title ?? $item->name,
                'type' => $result->type,
                'image' => $item->images['image'] ?? $item->images['banner'] ?? $item->image_url ?? $item->banner_url ?? null,
                'description' => str($item->description ?? $item->about ?? $item->intro ?? $item->details ?? $item->short_desc ?? '')->words(25)->__toString(),
            ];
        });

        return $this->buildResponse([
            'message' => __("Search for ':q' returned :count results", ['q' => $request->get('q'), 'count' => $results->count()]),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'results' => $results,
            'total' => $search->count(),
            'query' => $request->get('q'),
        ]);
    }
}
