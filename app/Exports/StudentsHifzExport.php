<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentsHifzExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return ['رقم_الطالب_المخفي', 'اسم الطالب', 'التاريخ', 'اسم السورة', 'من آية', 'إلى آية', 'ملاحظات المعلم'];
    }

    public function map($student): array
    {
        return [$student->id, $student->full_name, '', '', '', '', ''];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            
            $dateRange = 'C2:C200';
            $surahRange = 'D2:D200';

            $sheet->getStyle($dateRange)->getNumberFormat()->setFormatCode('dd-mm-yyyy');

            $dateValidation = $sheet->getDataValidation($dateRange);
            
            $dateValidation->setType(DataValidation::TYPE_TEXTLENGTH);
            
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setShowErrorMessage(true);
            
            $dateValidation->setFormula1('=ISNUMBER(C2)');
            
            $dateValidation->setErrorTitle('خطأ في الإدخال');
            $dateValidation->setError('يمنع كتابة الحروف هنا! يرجى إدخال التاريخ بالأرقام فقط (مثال: 21-04-2026)');
            
            $dateValidation->setPromptTitle('تنسيق التاريخ');
            $dateValidation->setPrompt('أدخل التاريخ بالأرقام (يوم-شهر-سنة)');

            $surahs = ['الفاتحة','البقرة','آل عمران','النساء','المائدة','الأنعام','الأعراف','الأنفال','التوبة','يونس','هود','يوسف','الرعد','إبراهيم','الحجر','النحل','الإسراء','الكهف','مريم','طه','الأنبياء','الحج','المؤمنون','النور','الفرقان','الشعراء','النمل','القصص','العنكبوت','الروم','لقمان','السجدة','الأحزاب','سبأ','فاطر','يس','الصافات','ص','الزمر','غافر','فصلت','الشورى','الزخرف','الدخان','الجاثية','الأحقاف','محمد','الفتح','الحجرات','ق','الذاريات','الطور','النجم','القمر','الرحمن','الواقعة','الحديد','المجادلة','الحشر','الممتحنة','الصف','الجمعة','المنافقون','التغابن','الطلاق','التحريم','الملك','القلم','الحاقة','المعارج','نوح','الجن','المزمل','المدثر','القيامة','الإنسان','المرسلات','النبأ','النازعات','عبس','التكوير','الانفطار','المطففين','الانشقاق','البروج','الطارق','الأعلى','الغاشية','الفجر','البلد','الشمس','الليل','الضحى','الشرح','التين','العلق','القدر','البينة','الزلزلة','العاديات','القارعة','التكاثر','العصر','الهمزة','الفيل','قريش','الماعون','الكوثر','الكافرون','النصر','المسد','الإخلاص','الفلق','الناس'];
            foreach ($surahs as $index => $name) {
                $sheet->setCellValue('Z' . ($index + 1), $name);
            }
            $surahValidation = $sheet->getDataValidation($surahRange);
            $surahValidation->setType(DataValidation::TYPE_LIST);
            $surahValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $surahValidation->setFormula1('$Z$1:$Z$114');
            $surahValidation->setShowDropDown(true);
            $surahValidation->setShowErrorMessage(true);
            $surahValidation->setError('يرجى اختيار السورة من القائمة');
        },
    ];
}
}