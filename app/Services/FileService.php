<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FileService
{
    private LinkService $linkService;
    public function __construct(LinkService $linkService) {
        $this->linkService = $linkService;
    }

    /**
     * @return BinaryFileResponse
     */
    public static function downloadCsv(): BinaryFileResponse
    {
        $table = Link::with('creator')->where('creator_id', Auth::id())->get();
        $filename = "report.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array(
            'website_url',
            'short_link',
            'clicks',
            'ips',
            'creator_name',
            'creator_email',
            'created_at',
            'updated_at'
        ));

        foreach ($table as $row) {
            fputcsv($handle, array(
                $row->website_url,
                $row->getUrl(),
                $row->clicks,
                implode(',', array_column($row->views->toArray(), 'ip')),
                $row->creator->full_name,
                $row->creator->email,
                $row->created_at,
                $row->updated_at
            ));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'report.csv', $headers);
    }

    /**
     * @param Request $request
     * @throws FileNotFoundException
     */
    function uploadCSV(Request $request): void
    {
        $path = $this->putFileToLocal($request);
        $path = storage_path($path);
        $fp = fopen($path, 'r');
        while ($line = fgetcsv($fp)) {
            $websiteUrl = $line[0];
            $this->linkService->shorten($request->domain, $websiteUrl);
        }
        fclose($fp);
    }

    /**
     * @param Request $request
     * @return string
     * @throws FileNotFoundException
     */
    public function putFileToLocal(Request $request): string
    {
        $file = $request->file('upload-csv');
        $content = $file->get();
        $fileName = $file->getClientOriginalName();

        Storage::put($fileName, $content);
        return "app/$fileName";
    }
}
