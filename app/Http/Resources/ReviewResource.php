<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'stars' => $this->stars,
            'comment' => $this->comment,
            'book_id' => $this->whenLoaded('book', fn() => $this->book->id),
            'user_email' => $this->whenLoaded('user', fn() => $this->user->email)
        ];
    }
}
