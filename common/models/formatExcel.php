<?php
/**
 * Created by PhpStorm.
 * User: hungluong
 * Date: 5/24/17
 * Time: 9:34 AM
 */
namespace common\models;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class formatExcel
{
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     */
    public static function setDateValue($activeSheet, $range, $cell, $value = "2017-12-31", $format = "d/m/Y"){
        if($value != ""){
            $activeSheet
                ->getStyle($range)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $activeSheet->setCellValue($cell,$value!=""?date($format,strtotime($value)):"", true);
        }
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $cell string
     * @param string $value
     * @param string $format
     */
    public static function setDateValue2($activeSheet, $cell, $value = "2017-12-31", $format = "d/m/Y"){
        if($value != ""){
            $dateTimeNow = strtotime($value." 01:00:00 AM");
            $activeSheet
                ->setCellValue($cell, \PHPExcel_Shared_Date::PHPToExcel( $dateTimeNow ));
            $activeSheet
                ->getStyle($cell)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
        }
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     */
    public static function setPositionImage($activeSheet, $pathImage, $height = 0, $width = 0, $setResizeProportional = false, $coordinates = "A1", $name = "THDG TV", $description = "THDG TV", $offetX = 0, $offetY = 0){
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName($name);
        $objDrawing->setDescription($description);
        $objDrawing->setPath($pathImage);       // filesystem reference for the image file
        $objDrawing->setHeight($height);                 // sets the image height to 36px (overriding the actual image height);
        $objDrawing->setWidth($width);                 // sets the image height to 36px (overriding the actual image height);
        $objDrawing->setCoordinates($coordinates);    // pins the top-left corner of the image to cell D24
        $objDrawing->setOffsetX($offetX);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
        $objDrawing->setOffsetY($offetY);
//        $objDrawing->setResizeProportional(true);
//        $objDrawing->setWidthAndHeight($width,$height);
//        $objDrawing->setWidthAndHeight($width, $height);
        $objDrawing->setResizeProportional($setResizeProportional);
        $objDrawing->setWorksheet($activeSheet);
    }
    /**
     * @param $acticeSheet \PHPExcel_Worksheet
     */
    public static function setFontBold($acticeSheet, $range){
        $acticeSheet->getStyle($range)
            ->applyFromArray([
                'font' => [
                    'bold' => true
                ]
            ]);
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     */
    public static function alignCenterText($activeSheet, $range){
        $activeSheet->getStyle($range)->getAlignment()->applyFromArray(
            array('horizontal' => Alignment::HORIZONTAL_CENTER,) //\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     */
    public static function alignLeftText($activeSheet, $range){
        $activeSheet->getStyle($range)->getAlignment()->applyFromArray(
            array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
        );
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $columnStart string
     * @param $columnEnd string
     */
    public static function setAutoWidthColumnd($activeSheet, $columnStart = 'A', $columnEnd = 'Z'){
        foreach(range($columnStart,$columnEnd) as $columnID)
        {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $columnStart string
     * @param $columnEnd string
     * @param $width int
     */
    public static function setWidthColumn($activeSheet, $columnStart = 'A', $columnEnd = 'A', $width = -1){
        foreach(range($columnStart,$columnEnd) as $columnID)
        {
            $activeSheet->getColumnDimension($columnID)->setWidth($width);
        }
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $range string
     */
    public static function setWrapText($activeSheet, $range){
        $activeSheet->getStyle($range)->getAlignment()->setWrapText(true);
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $range string
     */
    public static function alignVerticalCenter($activeSheet, $range){
        $activeSheet->getStyle($range)->getAlignment()->applyFromArray(
            array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,)
        );
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $range string
     */
    public static function alignHorizontalLeft($activeSheet, $range){
        $activeSheet->getStyle($range)->getAlignment()->applyFromArray(
            array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
        );
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $range string
     */
    public static function setFontSize($activeSheet, $range, $size){
        $activeSheet->getStyle($range)->applyFromArray([
            'font' => [
                'size' => $size
            ]
        ]);
    }
    public static function setHeightRow($activeSheet, $row, $height = 40){
        $activeSheet->getRowDimension($row)->setRowHeight($height);
    }
    /**
     * @param $activeSheet Worksheet
     * @param $range string
     */
    public static function setBorder($activeSheet, $range){
        $activeSheet->getStyle($range)->applyFromArray([
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN //\PHPExcel_Style_Border::BORDER_THIN
                )
            )
        ]);
    }
    /**
     * @param $activeSheet Worksheet
     * @param $range
     * @param int $fontSize
     * @param string $fontFamily
     */
    public static function setFontFamily($activeSheet, $range, $fontSize = 12, $fontFamily='Times New Roman'){
        $activeSheet->getStyle($range)->getFont()->setName($fontFamily)->setSize($fontSize);
    }
    /**
     * @param $activeSheet Worksheet
     * @param $range
     * @param $colorRGB
     */
    public static function setBgColor($activeSheet, $range, $colorRGB){
        $activeSheet
            ->getStyle($range)->getFill()->applyFromArray(array(
                'type' =>  Fill::FILL_SOLID,//\PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => $colorRGB
                )
            ));
    }
    /**
     * @param $objWorkSheet
     * @param $index IOFactory
     */
    public static function deleteSheet($objWorkSheet, $index){
        $objWorkSheet->removeSheetByIndex($index);
    }
    /**
     * @param \PHPExcel_Worksheet $sheet
     * @param int $srcRow
     * @param int $dstRow
     * @param int $height
     * @param int $width
     */
    public static function copyRows($sheet,$srcRow,$dstRow,$height,$width) {
        for ($row = 0; $row < $height; $row++) {
            for ($col = 0; $col < $width; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $srcRow + $row);
                $style = $sheet->getStyleByColumnAndRow($col, $srcRow + $row);
                $dstCell = \PHPExcel_Cell::stringFromColumnIndex($col) . (string)($dstRow + $row);
                $sheet->setCellValue($dstCell, $cell->getValue());
                $sheet->duplicateStyle($style, $dstCell);
            }
            $h = $sheet->getRowDimension($srcRow + $row)->getRowHeight();
            $sheet->getRowDimension($dstRow + $row)->setRowHeight($h);
        }
        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $col_s = preg_replace("/[0-9]*/", "", $mc[0]);
            $col_e = preg_replace("/[0-9]*/", "", $mc[1]);
            $row_s = ((int)preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
            $row_e = ((int)preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;
            if (0 <= $row_s && $row_s < $height) {
                $merge = $col_s . (string)($dstRow + $row_s) . ":" . $col_e . (string)($dstRow + $row_e);
                $sheet->mergeCells($merge);
            }
        }
    }
    /**
     * @param $activeSheet \PHPExcel_Worksheet
     * @param $range string
     * @param $format string
     */
    public static function setFormatCell($activeSheet, $range, $format){
        $activeSheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
    }
}