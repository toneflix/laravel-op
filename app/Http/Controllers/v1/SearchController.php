<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use Illuminate\Http\Request;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $scope = $request->get('scope', ['user']);
        $private = str($request->get('private', false))->is(['true', '1', 'yes', 'on', true]);

        if (! is_array($scope)) {
            $scope = [$scope];
        }

        if ($request->q) {
            $query = new Search();

            // if (in_array('category', $scope)) {
            //     $query->registerModel(Category::class, 'title', 'description');
            // }

            if (in_array('company', $scope)) {
                $query->registerModel(User::class, function (ModelSearchAspect $modelSearchAspect) use ($private) {
                    $modelSearchAspect
                    ->addSearchableAttribute('firstname')
                    ->addSearchableAttribute('lastname')
                    ->addSearchableAttribute('email')
                    ->addSearchableAttribute('about')
                    ->addSearchableAttribute('address')
                    ->addExactSearchableAttribute('country')
                    ->addExactSearchableAttribute('state')
                    ->addExactSearchableAttribute('city')
                    ->addExactSearchableAttribute('role')
                    ->where(function ($query) use ($private) {
                        if ($private) {
                            $query->where('id', auth()->id());
                        } else {
                            $query->where('id', '!=', null);
                        }
                    });
                });
            }

            $search = $query->limitAspectResults($request->get('limit', 25))->search($request->get('q', ''));
        } else {
            $search = collect([]);
        }

        $results = $search->map(function ($result) {
            // dd($result);
            $item = $result->searchable;

            return [
                'id' => $result->searchable->id,
                'key' => str($result->searchable->id)->append($result->type)->slug(),
                'url' => $result->url,
                'url' => ['company' => $item->company->slug ?? $item->shop->slug ?? null, 'item' => $item->slug],
                'title' => $result->title,
                'type' => $result->type === 'companies' ? $item->type : $result->type,
                'image' => $item->images['image'] ?? $item->images['banner'] ?? $item->image_url ?? $item->banner_url ?? null,
                'description' => str($item->description ?? $item->about ?? $item->intro ?? $item->details ?? $item->basic_info ?? $item->short_desc ?? '')->words(25)->__toString(),
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
