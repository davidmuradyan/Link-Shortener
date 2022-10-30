<?php

namespace App\Services;

use App\Http\Requests\LinkRequest;
use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LinkService
{
    /**
     * @param LinkRequest $request
     * @return array
     */
    public function shorten(LinkRequest $request): array
    {
        $domain = Domain::find($request->domain);
        $slug = Str::random(10). time(); // ToDo: can be implemented more unique slug

        $data = [
            'website_url' => $request->website_url,
            'domain_id' => $domain->id,
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
        $link->clicks += 1;
        $link->save();

        LinkView::create([
            'link_id' => $link->id,
            'ip' => $request->ip(),
        ]);

        return $link->website_url;
    }
}
