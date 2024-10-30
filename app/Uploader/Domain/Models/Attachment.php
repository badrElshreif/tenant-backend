<?php

namespace App\Uploader\Domain\Models;

use App\Infrastructure\Traits\UploaderHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory, UploaderHelper;

    protected $fillable = ['name', 'type', 'attachable_type', 'attachable_id', 'folder', 'description', 'is_visible_for_school'];

    protected $casts = [
        'is_visible_for_school' => 'boolean'
    ];


    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the attachable creator model like (admin).
     */
    public function creatable()
    {
        return $this->morphTo();
    }

    public function getFullPathAttribute()
    {
        $fullPath = $this->getFileFullPath($this->name, $this->folder);

        return $fullPath;
    }
}
