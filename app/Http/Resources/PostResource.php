<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Http\Resources\TagCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title'=> $this->title,
            'content' => $this->content,
            'likes' => $this->likes,
            'publishDate' => $this->publish_date,
            'isPublished' => $this->is_published,
            'user' => new UserResource($this->whenLoaded('user')),
            'tags' => new TagCollection($this->whenLoaded('tags'))
        ];
    }

}
