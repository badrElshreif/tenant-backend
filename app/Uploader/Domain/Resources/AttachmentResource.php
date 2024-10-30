<?php

namespace App\Uploader\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file' => $this->full_path,
            'folder' => $this->folder,
            'type' => $this->type,
            'description' => $this->description
        ];
    }
}
