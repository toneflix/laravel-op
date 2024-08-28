<?php

namespace App\Http\Resources;

use ToneflixCode\ResourceModifier\Services\Json\ResourceCollection;
use Illuminate\Http\Request;

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
