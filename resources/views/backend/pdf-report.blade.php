<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        td, th {
            border: 1px solid black;
            padding: 4px;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
        {{ config('app.name') }}
    </htmlpageheader>

    <h1>Report</h1>
    @include('backend.include.report')

    <htmlpagefooter name="page-footer">
        {{ now()->toUserTimezone()->isoFormat('LLLL') }}
    </htmlpagefooter>
</body>
</html>