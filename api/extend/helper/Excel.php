<?php
namespace helper;

/*
 * phpexcel数据导出
 */
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel;

class Excel
{
	//导出记录表格
	public static function exportExcel($rowTitle = [],$data = [])
	{
		// Create new PHPExcel object
		$objPhpExcel = new PHPExcel();
		$objPhpExcel->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);//设置单元格宽度
		//设置标题
		foreach ($rowTitle as $k => $r) {
			$objPhpExcel->getActiveSheet()
					->getStyleByColumnAndRow($k, 1)
					->getFont()->setBold(true);//字体加粗
			$objPhpExcel->getActiveSheet()
					->getStyleByColumnAndRow($k, 1)
					->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//文字居中
			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($k, 1, $r);
		}
		//设置当前的sheet索引和名称
		$title = '汇总表';
		$objPhpExcel->setActiveSheetIndex(0);
		$objActSheet = $objPhpExcel->getActiveSheet();
		$objActSheet->setTitle($title);

    $abc = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
		//设置单元格内容
		$columnCount = count($rowTitle);  //列数
		foreach ($data as $k => $v) {
			$num = $k +2;
			for ($i = 0; $i < $columnCount; $i++) {
				$objPhpExcel->setActiveSheetIndex(0)->setCellValue($abc[$i] . $num, $v[$rowTitle[$i]]);
			}
		}
		$name = date('Y-m-d');//设置文件名
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Transfer-Encoding:utf-8");
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $title . '_' . urlencode($name) . '.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel5');
		$objWriter->save('php://output');
	}

}
