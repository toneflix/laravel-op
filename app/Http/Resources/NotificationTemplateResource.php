<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'subject' => $this->subject,
            'plain' => $this->plain,
            'html' => $this->html,
            'args' => $this->args,
            'active' => $this->active,
            'allowed' => $this->allowed,
        ];
    }
}