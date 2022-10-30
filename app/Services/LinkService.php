<?php

namespace App\Services;

use App\Models\Link;
use App\Models\LinkView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LinkService
{
    /**
     * @param $domain
     * @param $websiteUrl
     * @return array
     */
    public function shorten($domain, $websiteUrl): array
    {
        $slug = Str::random(10). time(); // ToDo: can be implemented more unique slug

        $data = [
            'website_url' => $websiteUrl,
            'domain_id' => $domain,
            'slug' => $slug,
            'tracking_data' => '',
            'clicks' => 0,
            'creator_id' => Auth::id(),
        ];
        $link = Link::create($data);

        $shortLink = $link->getUrl();
        $qrCode = QrCode::size(250)->generate($shortLink);
        return ['shortLink' => $shortLink, 'qrCode' => $qrCode];
    }

    /**
     * @param Request $request
     * @param Link $link
     * @return string
     */
    public function viewLink(Request $request, Link $link): string
    {
        try {
            $link->clicks += 1;
            $link->save();

            LinkView::create([
                'link_id' => $link->id,
                'ip' => $request->ip(),
            ]);
            return $link->website_url;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
