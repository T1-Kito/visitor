@props(['status'])

@php
    $map = [
        'pending' => ['label' => 'Chờ duyệt', 'class' => 'status-pending'],
        'approved' => ['label' => 'Đã duyệt', 'class' => 'status-approved'],
        'rejected' => ['label' => 'Từ chối', 'class' => 'status-rejected'],
        'checked_in' => ['label' => 'Đang trong công ty', 'class' => 'status-checked-in'],
        'checked_out' => ['label' => 'Đã rời công ty', 'class' => 'status-checked-out'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'status-cancelled'],
        'waiting' => ['label' => 'Yêu cầu chờ', 'class' => 'status-pending'],
        'active' => ['label' => 'Đang hoạt động', 'class' => 'status-approved'],
        'inactive' => ['label' => 'Ngừng hoạt động', 'class' => 'status-checked-out'],
        'available' => ['label' => 'Sẵn sàng cấp', 'class' => 'status-approved'],
        'revoked' => ['label' => 'Đã thu hồi', 'class' => 'status-checked-out'],
    ];

    $meta = $map[$status] ?? ['label' => str_replace('_', ' ', (string) $status), 'class' => 'status-default'];
@endphp

<span {{ $attributes->merge(['class' => 'status-badge '.$meta['class']]) }}>
    {{ $meta['label'] }}
</span>
