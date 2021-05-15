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
            'User',
            'Content',
            'Registered',
        ];
    }

    public function map($comment): array
    {
        $commentable = $comment->commentable;
        if ($commentable instanceof Customer) {
            $commentable_label = "$commentable->name, $commentable->id_number";
        } else {
            $commentable_label = '?';
        }
        return [
            $comment->id,
            optional($commentable)->id,
            $commentable_label,
            optional($comment->user)->name,
            $comment->content,
            $this->mapDateTime($comment->created_at),
        ];
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
