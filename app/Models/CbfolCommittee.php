<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbfolCommittee extends Model
{
    use HasFactory;

    protected $table = 'cbfol_committees';

    protected $fillable = [
        'name',
        'title',
        'category',
        'term',
        'term_year',
        'organization',
        'photo',
        'phone',
        'bio',
        'sort_order',
        'status',
    ];

    protected $appends = [
        'photo_url',
        'category_label',
        'status_label',
    ];

    public const CATEGORY_PRESIDENT = 'president';
    public const CATEGORY_STANDING = 'standing';
    public const CATEGORY_MEMBER = 'member';
    public const CATEGORY_ADVISOR = 'advisor';

    public static function categories(): array
    {
        return [
            self::CATEGORY_PRESIDENT => [
                'label' => 'ປະທານ-ຮອງປະທານ',
                'description' => 'ປະທານ ແລະ ຄະນະປະທານ ສູນກາງ ອພສ',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300',
            ],
            self::CATEGORY_STANDING => [
                'label' => 'ຄະນະປະຈຳການ',
                'description' => 'ຄະນະບໍລິຫານງານປະຈຳການ ສູນກາງ ອພສ',
                'badge_color' => 'bg-orange-100 text-orange-800 border-orange-300',
            ],
            self::CATEGORY_MEMBER => [
                'label' => 'ຄະນະກຳມະການ',
                'description' => 'ຄະນະກຳມະການບໍລິຫານງານ ສູນກາງ ອພສ',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300',
            ],
            self::CATEGORY_ADVISOR => [
                'label' => 'ຄະນະທີ່ປຶກສາ',
                'description' => 'ຄະນະທີ່ປຶກສາ ສູນກາງ ອພສ',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            ],
        ];
    }

    public static function terms(): array
    {
        return [
            'ສະໄໝທີ IX',
            'ສະໄໝທີ VIII',
            'ສະໄໝທີ VII',
        ];
    }

    public static function formatPhotoUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        // Convert any Google Drive sharing / view URL to direct high-speed embed URL
        if (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:.*&)?id=)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://lh3.googleusercontent.com/d/{$matches[1]}";
        }

        return $url;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        $formatted = self::formatPhotoUrl($this->photo);

        if (str_starts_with($formatted, 'http://') || str_starts_with($formatted, 'https://')) {
            return $formatted;
        }

        return "/storage/{$formatted}";
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category]['label'] ?? (string) ($this->category ?? '');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'ກຳລັງດຳລົງຕຳແໜ່ງ' : 'ພົ້ນຕຳແໜ່ງ';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('organization', 'like', "%{$search}%")
                ->orWhere('term', 'like', "%{$search}%");
        });
    }
}
