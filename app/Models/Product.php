<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'image_url',
        'price',
        'stock',
        'category_id',
        'featured',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'featured' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }
}

