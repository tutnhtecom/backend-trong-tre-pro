<?php
namespace common\models;
/**
 * @property \PHPExcel $objPHPExcel
 */

use backend\models\ChiLuong;
use backend\models\DonDichVu;
use backend\models\LichSuTrangThaiDon;
use backend\models\TienDoKhoaHoc;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\base\Exception;
use yii\helpers\VarDumper;

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

class exportExcelBaoCaoDoanhThuTheoNgay
{
    public $data;
    public $path_file;
    public $creator = 'TRONG TRE PRO SETE CO.,Ltd';
    public $title = 'Báo cáo doanh thu theo ngay';
    public $subject = 'Báo cáo doanh thu theo ngay';
    public $description = '';
    public $category = '';
    public $keywords = '';
    public $sheetTitle = 'bao cao doanh thu theo ngay';
    public $legal = 'bao cao doanh thu theo ngay';
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
        $dong = 10;
        $sum = 0;
      /** @var TienDoKhoaHoc $item */
        foreach ($data['data'] as $item) {
          $donDichVu =$item->donDichVu;
          $giaoVien =$donDichVu->giaoVien;
          $sum += $donDichVu->so_buoi>0?$donDichVu->tong_tien/$donDichVu->so_buoi:0;
            $activeSheet
                ->setCellValue("A{$dong}",date("d/m/y H:i",strtotime($item->created)))
                ->setCellValue("B{$dong}", $giaoVien->hoten)
                ->setCellValue("C{$dong}",$giaoVien->dien_thoai)
                ->setCellValue("D{$dong}",$item->donDichVu->ma_don_hang)
                ->setCellValue("E{$dong}",$item->donDichVu->dichVu->ten_dich_vu)
                ->setCellValue("F{$dong}",$donDichVu->so_buoi>0?$donDichVu->tong_tien/$donDichVu->so_buoi:0)
                ->setCellValue("G{$dong}",$item->buoi."/".$item->donDichVu->so_buoi)
                ->setCellValue("H{$dong}",$item->trang_thai==TienDoKhoaHoc::CHUA_DAY?"Chưa vào ca":$item->trang_thai)
            ;
            $dong++;
        }
        $dong--;
        formatExcel::setBorder($activeSheet, "A10:H{$dong}");
        $activeSheet
            ->setCellValue("D6","Từ ngày: ".$data['tuNgay'])
            ->setCellValue("E6","Đến ngày: ".$data['denNgay'])
            ->setCellValue("E7",$sum)
        ;
    }
    public function run()
    {
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load(dirname(dirname(__DIR__)).'/common/template/BaoCaoDoanhThuTheoNgay.xlsx');
        $activeSheet = $objPHPExcel->getActiveSheet();
        $this->renderBody($activeSheet);
        $this->filename = 'BaoCaoDoanhThuTheoNgay_'.date('d_m_Y__H_i_s').'.xlsx';
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
