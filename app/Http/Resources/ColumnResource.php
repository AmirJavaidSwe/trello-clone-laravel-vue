<?php

namespace App\Http\Resources;

use App\Http\Resources\CardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ColumnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!$this->resource && !$this->resource->id) {
            return [];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'editableTitle' => $this->editableTitle,
            'cards' => CardResource::collection($this->cards),
        ];
    }
}
