<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DiamondExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithTitle, WithEvents, WithCustomStartCell, ShouldAutoSize, WithCustomValueBinder, WithColumnFormatting
{
    public $stone_id;

    public function __construct($stone_id)
    {
        $this->stone_id = $stone_id;
    }
    public function startCell(): string
    {
        return 'A5';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'L' => NumberFormat::FORMAT_NUMBER_00,
            'M' => NumberFormat::FORMAT_NUMBER_00,
            'N' => NumberFormat::FORMAT_NUMBER_00,
            'O' => NumberFormat::FORMAT_NUMBER_00,
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER_00,
            'R' => NumberFormat::FORMAT_NUMBER_00,
            'X' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function collection()
    {products
        $Product = Product::leftjoin('shapes', 'shapes.id','=','.shape_id')
        ->leftjoin('colors', 'colors.id','=','products.color_id')
        ->leftjoin('clarities', 'clarities.id','=','products.clarity_id')
        ->leftjoin('finishes as cut', 'cut.id','=','products.cut_id')
        ->leftjoin('finishes as pol', 'pol.id','=','products.polish_id')
        ->leftjoin('finishes as sym', 'sym.id','=','products.symmetry_id')
        ->leftjoin('fluorescences as flo', 'flo.id','=','products.fluorescence_id')

        ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY products.id DESC) AS Rowdata'), 'products.stone_id',
        DB::raw("case when products.cert_type=1 then 'IGI' when products.cert_type=2 then 'GIA' when products.cert_type=3 then 'HRD' when products.cert_type=4 then 'Delight Grading' else 0 end cert_type"),
        'shapes.name as shape_name','products.carat', 'colors.name as color_name',
        'clarities.name as clarity_name','cut.name as cut_name','pol.name as pol_name','sym.name as sym_name','flo.name as flo_name','products.rapo_rate','products.discount','products.rate','products.amount',
        'products.cert_no','products.depth','products.ratio','products.table','products.measurement',
        'products.image','products.video','products.cert_url','products.rapo_amount'
        )
        ->whereIn('products.stone_id', $this->stone_id)->get();
        // dd($Product);
        foreach($Product as &$pro) {
            $pro['image'] = '=HYPERLINK("'. $pro->image .'","IMAGE")';
            $pro['video'] = '=HYPERLINK("'. $pro->video .'","VIDEO")';
            $pro['cert_url'] = '=HYPERLINK("'. $pro->cert_url .'","CERT")';
        }
        return $Product;
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

                $sheet->mergeCells('E1:N1');
                $sheet->getStyle('E1')->getFont()->setBold(true);
                $sheet->setCellValue('E1', "delightdiamonds.com");
                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A1:X1')->applyFromArray($styleArray);
                $event->sheet->styleCells(
                    'E2:N2',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'A6A6A6',
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
                    'E3:N3',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'F2F2F2',
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
                    'E4:N4',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'D9D9D9',
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
                    'A5:X5',
                    [
                        'font' => [
                            'size'  =>  12,
                            'bold'  =>  true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'A6A6A6',
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
                $sheet->setAutoFilter('B5:X5');
                $sheet->setCellValue('E3', "All");
                $sheet->setCellValue('F2', "No.of.Pcs");
                $sheet->setCellValue('G2', "Weight");
                $sheet->setCellValue('J2', "RAP AVG");
                $sheet->setCellValue('K2', "RAP TOTAL");
                $sheet->setCellValue('L2', "AVG DIS%");
                $sheet->setCellValue('M2', "AVG P.CT");
                $sheet->setCellValue('N2', "TOTAL VL");
                $sheet->setCellValue('F3','=COUNT(A:A)');
                $sheet->setCellValue('G3','=ROUND(SUM(E:E),2)');
                $sheet->setCellValue('J3','=ROUND(K3/G3,2)');
                $sheet->setCellValue('K3','=ROUND(SUM(X:X),2)');
                $sheet->setCellValue('L3','=ROUND((0-100-(0-M3/J3)*100),2)');
                $sheet->setCellValue('M3','=ROUND(N3/G3,2)');
                $sheet->setCellValue('N3','=ROUND(SUM(O:O),2)');
            },
        ];
    }

    public function headings(): array
    {
        return [
            ['SrNo','Stone Id','Cert','Shape','Carat','Col'
            ,'Clarity','Cut','Pol','Sym','Flo','Rap','Disc%','GRate','Amount',
            'Report No', 'Depth','Ratio','Table','Measurement', /*'shd','Milk', 'EyeClean',*/ 'Image','Movie','Certi','Rap Value'
            ]
        ];
    }
}
