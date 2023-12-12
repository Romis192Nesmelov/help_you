<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'image' => $this->image ? asset($this->image) : '',
            'body' => $this->body,
            'author_id' => $this->user->id,
            'avatar' => $this->user->avatar ? asset($this->user->avatar) : asset('images/def_avatar.svg'),
            'avatar_props' => $this->user->avatar_props,
            'author' => $this->user->name.' '.$this->user->family,
            'date' => getRussianDate($this->created_at->timestamp),
            'time' => $this->created_at->format('H:m')
        ];
    }
}
