<?php

namespace App\BusinessLogic;

use Illuminate\Support\Facades\Response;

class ExportExcel
{
    /**
     * @param string $fileName اسم الملف عند التحميل
     * @param string $reportTitle العنوان الذي يظهر في أول صف مدمج
     * @param array $headers عناوين الأعمدة
     * @param array $data المصفوفة التي تحتوي البيانات
     * @param array $columnsMapping الحقول المقابلة في المصفوفة
     */
    public function export($fileName, $reportTitle, $headers, $data, $columnsMapping)
    {
        $columnCount = count($headers);

        $output = '
        <html dir="rtl">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style>
                /* تنسيق العنوان الرئيسي */
                .title-row {
                    font-size: 16pt;
                    font-weight: bold;
                    text-align: center;
                    height: 40px;
                }
                /* تنسيق صف الرأس */
                .header-row {
                    background-color: #1d6f42;
                    color: #ffffff;
                    font-weight: bold;
                    text-align: center;
                }
                /* تنسيق الخلايا العام */
                td {
                    border: 0.5pt solid #000000;
                    padding: 4px;
                    vertical-align: middle;
                }
                /* منع تحويل الأرقام الطويلة (هوية، هاتف) إلى ترميز علمي */
                .text-format { mso-number-format:"\@"; text-align: right; }
            </style>
        </head>
        <body>
            <table>
                <tr>
                    <td colspan="' . $columnCount . '" class="title-row">' . $reportTitle . '</td>
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

                // فحص القيمة: إذا كانت تبدأ بـ 0 أو كانت طويلة جداً، نعاملها كنص
                if (is_numeric($value) && (strlen($value) > 9 || strpos($value, '0') === 0)) {
                    $output .= '<td class="text-format">' . $value . '</td>';
                } else {
                    $output .= '<td>' . $value . '</td>';
                }
            }
            $output .= '</tr>';
        }

        $output .= '</table></body></html>';

        return Response::make($output, 200, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$fileName}.xls",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
