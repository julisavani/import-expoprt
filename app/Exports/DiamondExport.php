<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
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
    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }
    public function startCell(): string
    {
        return 'A5';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        //     'B' => NumberFormat::FORMAT_NUMBER,
        //     'C' => NumberFormat::FORMAT_TEXT,
        //     'D' => NumberFormat::FORMAT_TEXT,
        //     'E' => NumberFormat::FORMAT_NUMBER_00,
        //     'L' => NumberFormat::FORMAT_NUMBER_00,
        //     'M' => NumberFormat::FORMAT_NUMBER_00,
        //     'N' => NumberFormat::FORMAT_NUMBER_00,
        //     'O' => NumberFormat::FORMAT_NUMBER_00,
        //     'P' => NumberFormat::FORMAT_NUMBER,
        //     'Q' => NumberFormat::FORMAT_NUMBER_00,
        //     'R' => NumberFormat::FORMAT_NUMBER_00,
        //     'X' => NumberFormat::FORMAT_NUMBER_00,
        ];
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

                if($this->details['dt'] == 1){
                    $event->sheet->styleCells(
                        'A5:AX5',
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
                    $sheet->setAutoFilter('B5:AX5');
                    $sheet->setCellValue('E3', "All");
                    $sheet->setCellValue('F2', "No.of.Pcs");
                    $sheet->setCellValue('G2', "Weight");
                    $sheet->setCellValue('J2', "RAP AVG");
                    $sheet->setCellValue('K2', "RAP TOTAL");
                    $sheet->setCellValue('L2', "AVG DIS%");
                    $sheet->setCellValue('M2', "AVG P.CT");
                    $sheet->setCellValue('N2', "TOTAL VL");
                    $sheet->setCellValue('F3','=COUNT(A:A)');
                    $sheet->setCellValue('G3','=ROUND(SUM(H6:H1000),2)');
                    $sheet->setCellValue('J3','=ROUND(K3/G3,2)');
                    $sheet->setCellValue('K3','=ROUND(SUM(R6:R1000),2)');
                    $sheet->setCellValue('L3','=IF(J3 > 0, ROUND((0-100-(0-M3/J3)*100),2),0)');
                    $sheet->setCellValue('M3','=ROUND(N3/G3,2)');
                    $sheet->setCellValue('N3','=ROUND(SUM(U6:U1000),2)');
                }

                if($this->details['dt'] == 2){
                    $event->sheet->styleCells(
                        'A5:AM5',
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
                    $sheet->setAutoFilter('B5:AM5');
                    $sheet->setCellValue('E3', "All");
                    $sheet->setCellValue('F2', "No.of.Pcs");
                    $sheet->setCellValue('G2', "Weight");
                    $sheet->setCellValue('J2', "RAP AVG");
                    $sheet->setCellValue('K2', "RAP TOTAL");
                    $sheet->setCellValue('L2', "AVG DIS%");
                    $sheet->setCellValue('M2', "AVG P.CT");
                    $sheet->setCellValue('N2', "TOTAL VL");
                    $sheet->setCellValue('F3','=COUNT(A:A)');
                    $sheet->setCellValue('G3','=ROUND(SUM(H6:H1000),2)');
                    $sheet->setCellValue('J3','=ROUND(K3/G3,2)');
                    $sheet->setCellValue('K3','=ROUND(SUM(S6:S1000),2)');
                    $sheet->setCellValue('L3','=IF(J3 > 0, ROUND((0-100-(0-M3/J3)*100),2),0)');
                    $sheet->setCellValue('M3','=ROUND(N3/G3,2)');
                    $sheet->setCellValue('N3','=ROUND(SUM(V6:V1000),2)');
                }
            },
        ];
    }

    public function collection()
    {
        if($this->details['dt'] == 1){
            $Product = Product::leftjoin('shapes', 'shapes.id','=','products.shape_id')
            ->leftjoin('colors', 'colors.id','=','products.color_id')
            ->leftjoin('clarities', 'clarities.id','=','products.clarity_id')
            ->leftjoin('finishes as cut', 'cut.id','=','products.cut_id')
            ->leftjoin('finishes as pol', 'pol.id','=','products.polish_id')
            ->leftjoin('finishes as sym', 'sym.id','=','products.symmetry_id')
            ->leftjoin('fluorescences as flo', 'flo.id','=','products.fluorescence_id')
            ->leftjoin('fancy_colors as color', 'color.id','=','products.colors_id')
            ->leftjoin('fancy_colors as ove', 'ove.id','=','products.overtone_id')
            ->leftjoin('fancy_colors as ins', 'ins.id','=','products.intensity_id')

            ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY products.id DESC) AS Rowdata'), 'products.availability', 'products.country', 'products.stone_id',
            DB::raw("case when products.cert_type=1 then 'IGI' when products.cert_type=2 then 'GIA' when products.cert_type=3 then 'HRD' when products.cert_type=4 then 'Delight Grading' else 0 end cert_type"),
            'products.cert_no', 'shapes.name as sname','products.carat', 'colors.name as cname', 'color.name as coame', 'ins.name as iname', 'ove.name as oname', 'clarities.name as clname',
            'cut.name as cuname','pol.name as poname','sym.name as syname','flo.name as flname', 'products.rapo_rate','products.discount','products.rate','products.amount',
            'products.length', 'products.width', 'products.height', 'products.table_per', 'products.depth_per', 'products.key_to_symbols', 'products.milky', 'products.shade',
            'products.table_black','products.side_black', 'products.white_table', 'products.white_side', 'products.eye_clean', 'products.ratio', 'products.crown_angle', 'products.crown_height',
            'products.pavilion_angle', 'products.pavilion_height','products.girdle', 'products.table_open','products.pavilion_open','products.crown_open', 'products.girdle_desc',

            'products.comment','products.image','products.video', 'products.cert_url')
            ->whereIn('products.stone_id', $this->details['stone_id'])->get();
            return $Product;
        }

        if($this->details['dt'] == 2){

            $Product = Product::leftjoin('shapes', 'shapes.id','=','products.shape_id')
            ->leftjoin('colors', 'colors.id','=','products.color_id')
            ->leftjoin('clarities', 'clarities.id','=','products.clarity_id')
            ->leftjoin('finishes as cut', 'cut.id','=','products.cut_id')
            ->leftjoin('finishes as pol', 'pol.id','=','products.polish_id')
            ->leftjoin('finishes as sym', 'sym.id','=','products.symmetry_id')
            ->leftjoin('sizes', 'sizes.id','=','products.size_id')
            ->leftjoin('fluorescences as bgm', 'bgm.id','=','products.bgm_id')
            ->leftjoin('fluorescences as fc', 'fc.id','=','products.fluorescence_color_id')
            ->leftjoin('fluorescences as flo', 'flo.id','=','products.fluorescence_id')

            ->leftjoin('fancy_colors as color', 'color.id','=','products.colors_id')
            ->leftjoin('fancy_colors as ove', 'ove.id','=','products.overtone_id')
            ->leftjoin('fancy_colors as ins', 'ins.id','=','products.intensity_id')
            ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY products.id DESC) AS Rowdata'), 'products.availability', 'products.country', 'products.stone_id',
            DB::raw("case when products.cert_type=1 then 'IGI' when products.cert_type=2 then 'GIA' when products.cert_type=3 then 'HRD' when products.cert_type=4 then 'Delight Grading' else 0 end cert_type"),
            'products.cert_no', 'shapes.name as sname','products.carat', 'colors.name as cname', 'color.name as coame', 'ins.name as iname', 'ove.name as oname', 'clarities.name as clname',
            'cut.name as cuname','pol.name as poname','sym.name as syname', 'flo.name as flname', 'fc.name as fcname', 'products.rapo_rate','products.discount','products.rate','products.amount',
            'products.measurement','products.ratio', 'bgm.name as bname', 'products.length', 'products.width', 'products.height', 'products.table_per', 'products.depth_per',
            'products.pair', 'products.h_a', 'products.city', 'products.eye_clean', 'products.growth_type', 'products.treatment', 'products.image', 'products.video', 'products.cert_url')
            ->whereIn('products.stone_id', $this->details['stone_id'])->get();
            return $Product;
        }
    }

    public function headings(): array
    {
        if($this->details['dt'] == 1){
            return [
                [
                    'SrNo', 'Status', 'Country', 'Stone Id', 'Lab', 'Report No', 'Shape', 'Carat', 'Color', 'Fancy Color', 'Intensity', 'Overtone','Clarity', 'Cut', 'Polish', 'Symmetry',
                    'Fluorescent', 'Rap', 'Discount %', 'Rate %/CT', 'Amount', 'Length', 'Width', 'Height', 'Table%', 'Depth%', 'Key To Symbol', 'Milky', 'Shade', 'Table Black',
                    'Side Black', 'White Table', 'White Side', 'EYE CLEAN', 'Ratio', 'H&A', 'Inscription', 'Crown Angle', 'Crown Height', 'Pav Angle', 'Pav Height', 'Girdle%',
                    'Table Open', 'Pav Open', 'Crown Open', 'Gridle Desc', 'Comments', 'Image', 'Video', 'Certificate URL'
                ]
            ];
        }
        if($this->details['dt'] == 2){
            return [
                [
                    'SrNo', 'Status', 'Country', 'Stone Id', 'Lab', 'Report No', 'Shape', 'Carat', 'Color', 'Fancy Color', 'Intensity', 'Overtone', 'Clarity', 'Cut', 'Polish', 'Symmetry',
                    'Fluorescence Intensity', 'Fluorescence Color', 'Rap', 'Discount %', 'Rate $/CT', 'Amount', 'Measurement', 'Ratio', 'BGM', 'Length', 'Width', 'Height', 'TABLE%',
                    'DEPTH%', 'PAIR', 'H&A', 'City', 'EYE CLEAN', 'Growth Type', 'Treatment', 'Image', 'Video', 'Certificate URL'
                ]
            ];
        }
    }
}
