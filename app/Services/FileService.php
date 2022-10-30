<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FileService
{
    /**
     * @return BinaryFileResponse
     */
    public function downloadCsv(): BinaryFileResponse
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
}
