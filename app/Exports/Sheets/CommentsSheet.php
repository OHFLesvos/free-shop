<?php

namespace App\Exports\Sheets;

use App\Exports\DefaultWorksheetStyles;
use App\Models\Comment;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CommentsSheet implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Comments';

    public function query()
    {
        return Comment::orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Commentable ID',
            'Commentable',
            'Content',
            'User',
            'Date',
        ];
    }

    public function map($comment): array
    {
        $commentable = $comment->commentable;

        return [
            $comment->id,
            optional($commentable)->id,
            $this->getLabel($commentable),
            $comment->content,
            optional($comment->user)->name,
            $this->mapDateTime($comment->created_at),
        ];
    }

    private function getLabel($commentable): string
    {
        if ($commentable instanceof Customer) {
            return "$commentable->name, $commentable->id_number";
        }

        return '?';
    }

    private function mapDateTime($value)
    {
        return $value !== null
            ? Date::dateTimeToExcel($value->toUserTimezone())
            : null;
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
