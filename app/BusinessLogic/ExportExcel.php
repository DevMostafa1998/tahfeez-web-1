<?php

namespace App\BusinessLogic;

use Illuminate\Support\Facades\Response;

class ExportExcel
{
    public function export($fileName, $reportTitle, $headers, $data, $columnsMapping)
    {
        $columnCount = count($headers);

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
                    <th colspan="' . $columnCount . '" class="title-row">' . $reportTitle . '</th>
                </tr>
                <tr class="header-row">';
        foreach ($headers as $header) {
            $output .= '<td>' . $header . '</td>';
        }
        $output .= '</tr>';

        foreach ($data as $row) {
            $output .= '<tr>';
            foreach ($columnsMapping as $field) {
                $value = $row[$field] ?? '';
                $style = "";
                if (is_numeric($value) && (strlen($value) > 9 || strpos($value, '0') === 0)) {
                    $style = 'class="text-format"';
                }

                $output .= '<td ' . $style . '>' . $value . '</td>';
            }
            $output .= '</tr>';
        }

        $output .= '</table></body></html>';

        return Response::make($output, 200, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$fileName}.xls",
            "Cache-Control"       => "max-age=0",
        ]);
    }
}
