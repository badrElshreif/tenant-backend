<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id'   => $this->id,
            'name'   => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'title'   => $this->title,
            'body'    => $this->body,
            'type'    => new ContactTypeResource($this->type),
            'is_read' => $this->read_at ? true : false,
            'read_by' =>  optional($this->readBy)->name,
            'read_at' => optional($this->read_at)->format('Y-m-d'),
            'created_at' => optional($this->created_at)->format('Y-m-d'),
            'reply' => optional($this->replies->first())->body
            //'replies' => ContactUsResource::collection($this->replies)
        ]);
    }
}
