@props(['order' => null, 'value' => null])
@php
$status = $order != null ? $order->status : $value;
if ($status == 'new') {
$status_class = 'text-warning';
} elseif($status == 'ready') {
$status_class = 'text-info';
} elseif($status == 'completed') {
$status_class = 'text-success';
} elseif($status == 'cancelled') {
$status_class = 'text-danger';
}
@endphp
<span class="{{ $status_class }}">
    {{ ucfirst($status) }}
</span>
