<?php
namespace common\models;
/**
 * @property \PHPExcel $objPHPExcel
 */

use backend\models\DonDichVu;
use backend\models\LichSuTrangThaiDon;
use backend\models\QuanLyUserVaiTro;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\base\Exception;

/**
 * @author Nikola Kostadinov
 * @license MIT License
 * @version 0.3
 * @link http://yiiframework.com/extension/eexcelview/
 *
 * @fork 0.33ab
 * @forkversion 1.1
 * @author A. Bennouna
 * @organization tellibus.com
 * @license MIT License
 * @link https://github.com/tellibus/tlbExcelView
 */

class exportExcelPhieuLuong
{
    public $data;
    public $path_file;
    public $creator = 'TRONG TRE PRO SETE CO.,Ltd';
    public $title = 'Phieu Luong';
    public $subject = 'Phieu Luong';
    public $description = '';
    public $category = '';
    public $keywords = '';
    public $sheetTitle = 'Phieu Luong';
    public $legal = 'Phieu Luong';
    public $landscapeDisplay = false;
    public $A4 = false;
    public $RTL = false;
    public $pageFooterText = '&RPage &P of &N';

    //config
    public $autoWidth = true;
    public $exportType = 'Excel2007';
    public $disablePaging = true;
    public $filename = null; //export FileName
    public $stream = true; //stream to browser
    public $grid_mode = 'export'; //Whether to display grid ot export it to selected format. Possible values(grid, export)
    public $grid_mode_var = 'grid_mode'; //GET var for the grid mode

    //options
    public $automaticSum = false;
    public $sumLabel = 'Totals';
    public $decimalSeparator = '.';
    public $thousandsSeparator = ',';
    public $displayZeros = false;
    public $zeroPlaceholder = '-';
    public $border_style;
    public $borderColor = '000000';
    public $bgColor = 'FFFFFF';
    public $textColor = '000000';
    public $rowHeight = 15;
    public $headerBorderColor = '000000';
    public $headerBgColor = 'CCCCCC';
    public $headerTextColor = '000000';
    public $headerHeight = 20;
    public $footerBorderColor = '000000';
    public $footerBgColor = 'FFFFCC';
    public $footerTextColor = '0000FF';
    public $footerHeight = 20;
    public static $fill_solid;
    public static $papersize_A4;
    public static $orientation_landscape;
    public static $horizontal_center;
    public static $horizontal_right;
    public static $vertical_center;
    public static $horizontal_left;
    public static $style = array();
    public static $headerStyle = array();
    public static $footerStyle = array();
    public static $summableColumns = array();

    public static $objPHPExcel;
    public static $activeSheet;

    //buttons config
    public $exportButtonsCSS = 'summary';
    public $exportButtons = array('Excel2007');
    public $exportText = 'Export to: ';

    //callbacks
    public $onRenderHeaderCell = null;
    public $onRenderDataCell = null;
    public $onRenderFooterCell = null;

    //mime types used for streaming
    public $mimeTypes = array(
        'Excel5'	=> array(
            'Content-type'=>'application/vnd.ms-excel',
            'extension'=>'xls',
            'caption'=>'Excel(*.xls)',
        ),
        'Excel2007'	=> array(
            'Content-type'=>'application/vnd.ms-excel',
            'extension'=>'xlsx',
            'caption'=>'Excel(*.xlsx)',
        ),
        'PDF'		=>array(
            'Content-type'=>'application/pdf',
            'extension'=>'pdf',
            'caption'=>'PDF(*.pdf)',
        ),
        'HTML'		=>array(
            'Content-type'=>'text/html',
            'extension'=>'html',
            'caption'=>'HTML(*.html)',
        ),
        'CSV'		=>array(
            'Content-type'=>'application/csv',
            'extension'=>'csv',
            'caption'=>'CSV(*.csv)',
        )
    );


    /**
     * @param $activeSheet Worksheet
     */
    public function renderBody($activeSheet){
        $data = $this->data;
        $dong = 11;
        $count = 0;
        foreach ($data['donDichVu'] as $item) {
            $count++;
            $activeSheet
                ->setCellValue("E{$dong}","1.".($count))
                ->setCellValue("F{$dong}",$item['ma_don_hang'])
                ->setCellValue("G{$dong}",$item['so_buoi'])
                ->setCellValue("H{$dong}",$item['tong_tien'])
                  ;
            $dong++;
        }
        $activeSheet
            ->setCellValue("F5","Thời gian: ".$data['thoi_gian'])
            ->setCellValue("F6",$data['hoten'])
            ->setCellValue("F7",$data['id'])
            ->setCellValue("F8",$data['leader'])
            ->setCellValue("E{$dong}","1.".($count+1))
            ->setCellValue("F{$dong}","Làm thêm giờ")
            ->setCellValue("H{$dong}",$data['themGio'])
        ;
        $dong++;
        $activeSheet
            ->setCellValue("E{$dong}","2")
            ->setCellValue("F{$dong}","Tổng lương theo ngày công thực tế")
            ->setCellValue("H".($dong),$data['tongThucTe'])
        ;
        $dong++;
        $activeSheet
            ->setCellValue("E{$dong}","3")
            ->setCellValue("F{$dong}","Thu nhập bổ sung")
            ->setCellValue("H".($dong),$data['tongPhuPhi'])
        ;
        $dong++;
        $activeSheet
            ->setCellValue("E{$dong}","3.1")
            ->setCellValue("F{$dong}","Phụ cấp ăn trưa")
            ->setCellValue("H".($dong),$data['anTrua'])
        ;
        $count = 1;
        foreach ($data['phuPhiKhac'] as $item) {
            $count++;
            $dong++;
            $activeSheet
                ->setCellValue("E{$dong}","3.".($count))
                ->setCellValue("F{$dong}",$item['name'])
                ->setCellValue("H{$dong}",$item['so_tien'])
            ;
        }
        $dong++;
        $activeSheet
            ->setCellValue("E{$dong}","4")
            ->setCellValue("F{$dong}","Các khoản giảm trừ")
            ->setCellValue("H".$dong,$data['tongGiamTru'])
        ;
        $count = 0;
        foreach ($data['giamTru'] as $item) {
            $count++;
            $dong++;
            $activeSheet
                ->setCellValue("E{$dong}","4.".($count))
                ->setCellValue("F{$dong}",$item['name'])
                ->setCellValue("H{$dong}",$item['so_tien'])
            ;
        }
        $dong++;
        $activeSheet
            ->setCellValue("E{$dong}","5")
            ->setCellValue("F{$dong}","Thành tiền")
            ->setCellValue("H".$dong,$data['thanhTien'])
        ;
        formatExcel::setBorder($activeSheet, "E9:H{$dong}");
    }

    public function run()
    {
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load(dirname(dirname(__DIR__)).'/common/template/PhieuLuong.xlsx');
        $activeSheet = $objPHPExcel->getActiveSheet();
        $this->renderBody($activeSheet);
        $this->filename = 'Phieu_Luong_'.date('d_m_Y__H_i_s')."(".$this->data['hoten'].').xlsx';
        $this->path_file.=$this->filename;
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx'); //\PHPExcel_IOFactory::createWriter($objPHPExcel, $this->exportType);
        $objWriter->setPreCalculateFormulas(true);

        if (!$this->stream) {
            $objWriter->save($this->path_file);
        } else {
            //output to browser
            if(!$this->filename) {
                $this->filename = $this->title;
            }
            $this->cleanOutput();
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-type: '.$this->mimeTypes[$this->exportType]['Content-type']);
            header('Content-Disposition: attachment; filename="' . $this->filename . '.' . $this->mimeTypes[$this->exportType]['extension'] . '"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;
        }
        return $this->filename;
    }

    /**
     * Returns the corresponding Excel column.(Abdul Rehman from yii forum)
     *
     * @param int $index
     * @return string
     */
    public function columnName($index)
    {
        --$index;
        if (($index >= 0) && ($index < 26)) {
            return chr(ord('A') + $index);
        } else if ($index > 25) {
            return ($this->columnName($index / 26)) . ($this->columnName($index%26 + 1));
        } else {
            throw new Exception("Invalid Column # " . ($index + 1));
        }
    }

    /**
     * Performs cleaning on mutliple levels.
     *
     * From le_top @ yiiframework.com
     *
     */
    private static function cleanOutput()
    {
        for ($level = ob_get_level(); $level > 0; --$level) {
            @ob_end_clean();
        }
    }
}
