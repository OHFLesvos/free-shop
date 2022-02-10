<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait DefaultWorksheetStyles
{
    public function styles(Worksheet $sheet): void
    {
        // Sheet title
        $sheet->setTitle($this->worksheetTitle);

        // Orientation
        $sheet->getPageSetup()->setOrientation($this->orientation ?? PageSetup::ORIENTATION_LANDSCAPE);

        // Paper size
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        // Fit to page width
        $sheet->getPageSetup()->setFitToWidth(0);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Header: Centered: sheet name
        $sheet->getHeaderFooter()->setOddHeader('&C&A');

        // Footer: Left: date, right: current page / number of pages
        $sheet->getHeaderFooter()->setOddFooter('&L&D&R&P / &N');

        // Print header row on each page
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

        // Styling of header row
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
            ->getFont()
            ->setBold(true);

        // Borders
        $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        // Column alignments
        if (isset($this->columnAlignment)) {
            foreach ($this->columnAlignment as $column => $alignment) {
                $sheet->getStyle($column.'1:'.$column.$sheet->getHighestRow())
                    ->getAlignment()->setHorizontal($alignment);
            }
        }

        // Freeze first line
        $sheet->freezePane('B2');

        // Auto-filter
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
    }
}
