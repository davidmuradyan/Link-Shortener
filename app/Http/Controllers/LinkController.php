<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Models\Domain;
use App\Models\Link;
use App\Services\FileService;
use App\Services\LinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LinkController extends Controller
{
    public function index()
    {
        $domains = Domain::all()->toArray();
        return view('dashboard', compact(['domains']));
    }

    public function shorten(LinkRequest $request)
    {
        try {
            $data = (new LinkService)->shorten($request);
            $shortLink = $data['shortLink'];
            $qrCode = $data['qrCode'];
        } catch (\Exception $exception) {

        }
        return view('short_url', compact(['shortLink', 'qrCode']));
    }


    public function linksPage()
    {
        $data = Link::with('creator')->where('creator_id', Auth::id())->orderBy('created_at','desc')->get();
        return view('user_links', compact(['data']));
    }

    /**
     * @param Request $request
     * @param Link $link
     * @return RedirectResponse
     */
    public function viewLink(Request $request, Link $link): RedirectResponse
    {
        $website_url = (new LinkService())->viewLink($request, $link);

        return redirect()->to($website_url);
    }

    /**
     * @return BinaryFileResponse
     */
    public function allLinksCsv(): BinaryFileResponse
    {
        return (new FileService())->downloadCsv();
    }
}
