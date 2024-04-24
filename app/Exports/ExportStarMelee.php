<?php

namespace App\Exports;

use App\Models\StarMelee;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportStarMelee extends DefaultValueBinder implements FromCollection, WithTitle, WithEvents, WithCustomStartCell, ShouldAutoSize, WithCustomValueBinder, WithColumnFormatting
{
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_NUMBER,
            'M' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $StarMelee = StarMelee::where('status', 1)
        ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY id DESC) AS Rowdata'),DB::raw('case when shape="ROUND" then "ROUND" ELSE "FANCY LAYOUT" END AS Type'),DB::raw('case when shape="ROUND" then "" ELSE shape end AS Shape'),'size','sieve','carat','def_vvs_vs','def_vs_si','fg_vvs_vs','fg_vs_si','pink_vvs_vs_si1','yellow_vvs_vs_si1','blue_vvs_vs_si1')
        ->get();
        return $StarMelee;
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return 'delightdiamonds.com';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:H1');
                $sheet->mergeCells('I1:J1');
                $sheet->setCellValue('A1', "SR NO.");
                $sheet->setCellValue('B1', "Type");
                $sheet->setCellValue('C1', "Shape");
                $sheet->setCellValue('D1', "SIZE IN MM");
                $sheet->setCellValue('E1', "SIEVE SIZES");
                $sheet->setCellValue('F1', "CARAT WEIGHT");
                $sheet->setCellValue('G1', "DEF");
                $sheet->setCellValue('I1', "FGH");
                $sheet->setCellValue('K1', "PINK");
                $sheet->setCellValue('L1', "YELLOW");
                $sheet->setCellValue('M1', "BLUE");

                $sheet->setCellValue('G2', "VVS-VS");
                $sheet->setCellValue('H2', "VS-SI");
                $sheet->setCellValue('I2', "VVS-VS");
                $sheet->setCellValue('J2', "VS-SI");
                $sheet->setCellValue('K2', "VVS-VS-SI");
                $sheet->setCellValue('L2', "VVS-VS-SI");
                $sheet->setCellValue('M2', "VVS-VS-SI");

                $event->sheet->getDelegate()->getStyle('A1:M2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->styleCells(
                    'A1:M2',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'FFFF00',
                            ]
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'A2:M2',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => '70AD47',
                            ]
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ]
                );
            }

        ];
    }

    // public function headings(): array
    // {
    //     return [
    //         [
    //             'SR NO.','Type','Shape','SIZE IN MM','SIEVE SIZES','CARAT WEIGHT','DEF','FGH','PINK','YELLOW','BLUE'
    //         ],
    //         [
    //             'VVS-VS','VS-SI','VVS-VS','VS-SI','VVS-VS-SI','VVS-VS-SI','VVS-VS-SI'
    //         ]
    //     ];
    // }
}
