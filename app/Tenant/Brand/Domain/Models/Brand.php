<?php

namespace App\Tenant\Brand\Domain\Models;

use App\Infrastructure\Traits\UploaderHelper;
use App\Main\Tenant\Domain\Models\Tenant;
use App\Tenant\Product\Domain\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Brand extends Model
{
    use Translatable;
    use HasFactory;
    use Filterable;
    use UploaderHelper;

    private const DEFAULT_LOGO_PATH = 'assets/images/default/default-logo.png';
    private const DEFAULT_THUMBNAIL_SIZE = [
        'width' => 80,
        'height' => 80
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public array $translatedAttributes = ['name', 'description'];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<string>
     */
    protected $appends = ['min_img'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected  $casts = [
       // 'is_active' => 'boolean',
        'tax_percentage' => 'float'
    ];

    /**
     * Get the products associated with the brand.
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }

    /**
     * Scope a query to only include active/inactive brands.
     *
     * @param Builder $query
     * @param bool $isActive
     * @return Builder
     */
    public function scopeActive(Builder $query, bool $isActive): Builder
    {
        return $query->where('is_active', $isActive);
    }

    /**
     * Get the brand's thumbnail image URL.
     *
     * @return string
     */
    public function getMinImgAttribute(): string
    {
        return $this->getLogoUrl(
            self::DEFAULT_THUMBNAIL_SIZE['width'],
            self::DEFAULT_THUMBNAIL_SIZE['height']
        );
    }

    /**
     * Set the brand's image.
     *
     * @param string|null $value
     * @return void
     */
    protected function setImageAttribute(?string $value): void
    {
        if (!empty($value)) {
            $image = explode('/', $value);
            $this->attributes['image'] = end($image);
        }
    }

    /**
     * Get the brand's logo URL with specified dimensions.
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getLogoUrl(int $width, int $height): string
    {
        if (isset($this->image)) {
            return routeTenant('tenant.image.resize', [
                $width,
                $height,
                'brands',
                $this->image
            ]);
        }

        return asset(self::DEFAULT_LOGO_PATH);
    }
}
