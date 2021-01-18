@props(['order'])
@php
if ($order->status == 'new') {
$status_class = 'text-warning';
} elseif($order->status == 'ready') {
$status_class = 'text-info';
} elseif($order->status == 'completed') {
$status_class = 'text-success';
} elseif($order->status == 'cancelled') {
$status_class = 'text-danger';
}
@endphp
<span class="{{ $status_class }}">
    {{ ucfirst($order->status) }}
</span>
