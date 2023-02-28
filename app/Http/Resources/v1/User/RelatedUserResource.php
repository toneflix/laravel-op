<?php

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->followable_type === 'App\Models\v1\User') {
            return new UserResource($this->followable);
        } elseif (get_class($this->resource) === 'App\Models\v1\User') {
            return new UserResource($this->resource);
        }

        return parent::toArray($request);
    }
}