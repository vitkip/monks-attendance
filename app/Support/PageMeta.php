<?php

namespace App\Support;

use App\Models\News;
use Illuminate\Support\Str;

/**
 * Builds the server-rendered SEO / Open Graph meta payload for the root blade
 * template. The client-side <SeoHead> component covers in-app navigation, but
 * social crawlers (Facebook, LINE, Twitter, …) never execute JS, so without
 * these tags in the initial HTML a shared link shows the bare <title> and no
 * cover image. Controllers pass the result via ->withViewData('meta', …).
 */
class PageMeta
{
    public const SITE_NAME = 'ວັດປ່າໜອງບົວທອງໃຕ້';

    /**
     * Site-wide defaults, merged under every page's own overrides in app.blade.php.
     */
    public static function defaults(): array
    {
        return [
            'title' => self::SITE_NAME . ' — ພຣະສົງ, ພຸດທະສາສະໜາ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບວດຂາວ, ໄຫວ້ພຣະ',
            'description' => 'ເວັບໄຊທາງການ ວັດປ່າໜອງບົວທອງໃຕ້ ເມືອງສີໂຄດຕະບອງ ນະຄອນຫຼວງວຽງຈັນ ປະເທດລາວ — ຂ່າວສານ, ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ, ບວດຂາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບຸນປະເພນີ, ບົດສູດມົນໄຫວ້ພຣະ ແລະ ໂຄງການກໍ່ສ້າງພາຍໃນວັດ',
            'image' => asset('favicon-512x512.png'),
            'type' => 'website',
        ];
    }

    /**
     * Meta for a single news article — headline as the title, article excerpt as
     * the description, and the article's cover image as the share image.
     */
    public static function forArticle(News $article): array
    {
        $description = trim((string) $article->excerpt_or_summary);

        if ($description === '') {
            $description = Str::limit(
                trim(preg_replace('/\s+/', ' ', strip_tags((string) $article->content))),
                200
            );
        }

        return [
            'title' => $article->title . ' — ' . self::SITE_NAME,
            'description' => $description,
            'image' => $article->image_url ?: asset('favicon-512x512.png'),
            'type' => 'article',
        ];
    }
}
