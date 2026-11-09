<?php

namespace App\BusinessLogic;

use Illuminate\Support\Facades\Response;

class ExportExcel
{
    public function export($fileName, $reportTitle, $headers, $data, $columnsMapping)
    {
        $headersArray = is_array($headers) ? $headers : [];
        $columnCount = count($headersArray);

        if (empty($fileName)) {
            $fileName = 'تقرير_المحفظين_' . date('Y-m-d');
        }

        $output = '
        <html dir="rtl">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style>
                .title-row {
                    font-size: 16pt;
                    font-weight: bold;
                    text-align: center;
                    background-color: #f8f9fa;
                }
                .header-row td {
                    background-color: #007bff;
                    color: #ffffff;
                    font-weight: bold;
                    text-align: center;
                    border: 0.5pt solid #dee2e6;
                }
                td {
                    border: 0.5pt solid #dee2e6;
                    padding: 6px;
                    vertical-align: middle;
                    text-align: center;
                }
                .text-format { mso-number-format:"\@"; text-align: right; }

                .badge-custom {
                    background-color: #f0f0f0;
                    border: 1px solid #ccc;
                }
                .bg-info { background-color: #2faecb; color: white; }
                .bg-success { background-color: #28a745; color: white; }
            </style>
        </head>
        <body>
            <table>
                <tr>
                    <th colspan="' . $columnCount . '" class="title-row">' . htmlspecialchars($reportTitle) . '</th>
                </tr>
                <tr class="header-row">';

        foreach ($headersArray as $header) {
            $output .= '<td>' . htmlspecialchars($header) . '</td>';
        }
        $output .= '</tr>';

        foreach ($data as $row) {
            $output .= '<tr>';
            foreach ($columnsMapping as $field) {
                $value = $row[$field] ?? '';
                $style = "";

                if (is_numeric($value) && (strlen((string)$value) > 9 || strpos((string)$value, '0') === 0)) {
                    $style = 'class="text-format"';
                }

                $output .= '<td ' . $style . '>' . htmlspecialchars($value) . '</td>';
            }
            $output .= '</tr>';
        }

        $output .= '</table></body></html>';

        $cleanFileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);
        $encodedFileName = rawurlencode($cleanFileName) . '.xls';

        return Response::make($output, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$encodedFileName}\"; filename*=UTF-8''{$encodedFileName}",
            'Cache-Control'       => 'max-age=0, must-revalidate',
            'Pragma'              => 'public',
        ]);
    }
}
