<?php

namespace App\Exports;

use App\Models\TempProduct;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;

class HistoryExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithEvents
{
    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }
    public function collection()
    {
        if($this->details['dt'] == 1){
            $Product = TempProduct::where('uuid', $this->details['id'])
            ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY id DESC) AS Rowdata'),'availability', 'country', 'stone_id', 'cert_type', 'cert_no', 'shape_id', 'carat', 'color_id',
            'clarity_id', 'cut_id', 'polish_id', 'symmetry_id', 'fluorescence_id', 'rapo_rate', 'discount', 'rate', 'amount', 'table_per', 'depth_per', 'length','width', 'height',
            'key_to_symbols', 'milky', 'shade', 'table_black', 'side_black', 'white_table', 'white_side', 'eye_clean', 'ratio', 'h_a', 'inscription', 'crown_angle', 'crown_height',
            'pavilion_angle', 'pavilion_height', 'girdle', 'table_open', 'pavilion_open', 'crown_open', 'girdle_desc', 'comment', 'colors_id', 'intensity_id', 'overtone_id', 'image', 'video','cert_url')
            ->get();

            // foreach($Product as &$pro) {
            //     $pro['image'] = '=HYPERLINK("'. $pro->image .'","IMAGE")';
            //     $pro['video'] = '=HYPERLINK("'. $pro->video .'","VIDEO")';
            //     $pro['cert_no'] = '=HYPERLINK("'. $pro->cert_url .'","'. $pro->cert_no .'")';
            //     unset($pro['cert_url']);
            // }
            return $Product;
        }

        if($this->details['dt'] == 2){

            $Product = TempProduct::where('uuid', $this->details['id'])
            ->select(DB::raw('ROW_NUMBER() OVER(ORDER BY id DESC) AS Rowdata'),'availability', 'country', 'stone_id', 'cert_type', 'cert_no', 'shape_id', 'carat', 'color_id',
            'colors_id', 'intensity_id', 'overtone_id', 'clarity_id', 'cut_id', 'polish_id', 'symmetry_id', 'fluorescence_id', 'fluorescence_color_id', 'rapo_rate', 'discount', 'rate', 'amount',
            'measurement', 'ratio', 'bgm_id', 'length','width', 'height', 'table_per', 'depth_per', 'pair', 'h_a', 'city', 'eye_clean' ,'growth_type', 'treatment', 'image', 'video','cert_url')
            ->get();
            // foreach($Product as &$pro) {
            //     $pro['image'] = '=HYPERLINK("'. $pro->image .'","IMAGE")';
            //     $pro['video'] = '=HYPERLINK("'. $pro->video .'","VIDEO")';
            //     $pro['cert_no'] = '=HYPERLINK("'. $pro->cert_url .'","'. $pro->cert_no .'")';
            //     unset($pro['cert_url']);
            // }
            return $Product;
        }
    }
    public function registerEvents(): array
    {
        if($this->details['dt'] == 1){
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->styleCells(
                        'A1:AX1',
                        [
                            'font' => [
                                'size'  =>  12,
                                'bold'  =>  true,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => '8EA9DB',
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
                },
            ];
        }
        if($this->details['dt'] == 2){
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->styleCells(
                        'A1:AM1',
                        [
                            'font' => [
                                'size'  =>  10,
                                'bold'  =>  true,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => '8EA9DB',
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
                },
            ];
        }
    }

    public function headings(): array
    {
        if($this->details['dt'] == 1){
            return [
                [
                    'SR No', 'Status', 'Country', 'Stone Id', 'Lab', 'Report No', 'Shape', 'Carat', 'Color', 'Clarity', 'Cut', 'Polish', 'Symmetry', 'Fluorescent', 'Rap', 'Disc %',
                    'Price/Ct', 'Amount', 'Table%', 'Depth%', 'Length', 'Width', 'Height', 'Key To Symbol', 'Milky', 'Shade', 'Table Black', 'Side Black', 'White Table', 'White Side',
                    'EC', 'Ratio', 'H&A', 'Inscription', 'Crown Angle', 'Crown Height', 'Pav Angle', 'Pav Height', 'Girdle%', 'Table Open', 'Pav Open', 'Crown Open', 'Gridle Desc',
                    'Comments', 'Fancy Color', 'Fancy Color Intensity', 'Fancy Color Overtone', 'Image Link', 'Video Link','Certificate URL'
                ]
            ];
        }
        if($this->details['dt'] == 2){
            return [
                [
                    'SrNo','Status', 'Country', 'Stone ID', 'Lab', 'Report No', 'Shape', 'Carat', 'Color', 'Fancy Color', 'Fancy Color Intensity', 'Fancy Color Overtone', 'Clarity',
                    'Cut', 'Polish', 'Symmetry', 'Fluorescence Intensity', 'Fluorescence Color', 'Rap', 'Discount %', 'Rate $/CT', 'Amount', 'Measurement', 'Ratio', 'BGM',
                    'LENGTH', 'WIDTH', 'Height', 'TABLE%', 'DEPTH%', 'PAIR', 'H&A', 'City', 'EYE CLEAN', 'growth_typeGrowth Type', 'Treatment', 'Image', 'Video','Certificate URL'
                ]
            ];
        }
    }
}
