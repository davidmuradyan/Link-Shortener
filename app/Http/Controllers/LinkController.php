<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Models\Domain;
use App\Models\Link;
use App\Services\FileService;
use App\Services\LinkService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LinkController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $domains = Domain::all()->toArray();
        return view('dashboard', compact(['domains']));
    }

    /**
     * @param LinkRequest $request
     * @return View
     */
    public function shorten(LinkRequest $request): View
    {
        $data = (new LinkService)->shorten($request->domain, $request->website_url);
        $shortLink = $data['shortLink'];
        $qrCode = $data['qrCode'];
        return view('short_url', compact(['shortLink', 'qrCode']));
    }

    /**
     * @return View
     */
    public function linksPage(): View
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
        return FileService::downloadCsv();
    }

    /**
     * @return View
     */
    public function uploadCSVPage(): View
    {
        $domains = Domain::all()->toArray();
        return view('upload_csv', compact(['domains']));
    }

    /**
     * @param Request $request
     * @param FileService $fileService
     * @throws FileNotFoundException
     */
    public function uploadCSV(Request $request, FileService $fileService)
    {
        $fileService->uploadCSV($request);
        return redirect('/my-links');
    }
}
