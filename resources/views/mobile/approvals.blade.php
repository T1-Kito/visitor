@extends('layouts.mobile')

@section('title', 'Khách cần duyệt')

@push('styles')
    <style>
        .m-approval-sticky {
            position: sticky;
            top: 0;
            z-index: 12;
            margin: 0 -2px 10px;
            padding: 6px 0 8px;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(10px);
        }

        .m-approval-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 4px;
            min-height: 42px;
            padding: 4px;
            border: 1px solid #d8e6f5;
            border-radius: 16px;
            background: #f8fbff;
        }

        .m-approval-tab {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 34px;
            border: 0;
            border-radius: 12px;
            background: transparent;
            color: #64748b;
            font: inherit;
            font-size: .84rem;
            line-height: 1;
            white-space: nowrap;
        }

        .m-approval-tab.is-active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
        }

        .m-approval-count {
            display: inline-grid;
            min-width: 20px;
            height: 20px;
            place-items: center;
            padding: 0 6px;
            border-radius: 999px;
            background: #fff4bd;
            color: var(--m-primary);
            font-size: .72rem;
        }

        .m-approval-panel[hidden],
        .m-toast[hidden],
        .m-empty[hidden],
        .is-hidden {
            display: none !important;
        }

        .m-approval-card {
            transition: opacity .18s ease, transform .18s ease;
        }

        .m-approval-card.is-removing {
            opacity: 0;
            transform: translateY(8px);
        }

        .m-approval-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 8px;
            color: #6b7f99;
            font-size: .82rem;
        }

        .m-approval-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 9px;
            background: #fff8d6;
            color: var(--m-primary);
            font-size: .78rem;
        }

        .m-more-wrap {
            margin-top: 12px;
        }

        .m-more-btn {
            width: 100%;
            border: 1px solid #f0cf43;
            border-radius: 14px;
            background: #fff;
            color: #111827;
            padding: 10px 14px;
            font: inherit;
            font-size: .9rem;
        }
    </style>
@endpush

@section('content')
    @php
        $pendingTotal = count($pendingVisits);
        $approvedTotal = count($approvedVisits);
        $rejectedTotal = count($rejectedVisits);
        $pageSize = 5;
    @endphp

    <section class="m-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Khách cần duyệt</h1>
            <p>Host xử lý các yêu cầu thuộc mình.</p>
        </div>
    </section>

    <div class="m-approval-sticky">
        <div class="m-approval-tabs" role="tablist" aria-label="Trạng thái duyệt">
            <button class="m-approval-tab is-active" type="button" data-approval-tab="pending">
                Chờ duyệt
                <span class="m-approval-count" data-pending-count>{{ $pendingTotal }}</span>
            </button>
            <button class="m-approval-tab" type="button" data-approval-tab="approved">
                Đã xử lý
                <span class="m-approval-count" data-approved-count>{{ $approvedTotal }}</span>
            </button>
            <button class="m-approval-tab" type="button" data-approval-tab="rejected">
                Từ chối
                <span class="m-approval-count" data-rejected-count>{{ $rejectedTotal }}</span>
            </button>
        </div>
    </div>

    <div class="m-toast" data-approval-toast hidden>
        <i class="bi bi-check-circle"></i>
        <span></span>
    </div>

    @if (session('status'))
        <div class="m-toast"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
    @endif
    @if (session('error'))
        <div class="m-toast danger"><i class="bi bi-exclamation-triangle"></i><span>{{ session('error') }}</span></div>
    @endif

    <section class="m-section m-approval-panel" data-approval-panel="pending">
        <div class="m-section-head">
            <div>
                <h2>Chờ duyệt</h2>
                <span>Hiển thị từng nhóm {{ $pageSize }} lịch.</span>
            </div>
        </div>

        <div class="m-card-list" data-approval-list="pending">
            @foreach ($pendingVisits as $visit)
                <article
                    class="m-action-card m-approval-card {{ $loop->index >= $pageSize ? 'is-hidden' : '' }}"
                    data-approval-card
                    data-code="{{ e($visit['code']) }}"
                    data-visitor="{{ e($visit['visitor']) }}"
                    data-company="{{ e($visit['company']) }}"
                    data-host="{{ e($visit['host']) }}"
                    data-time="{{ e($visit['time']) }}"
                    data-date="{{ e($visit['date']) }}"
                    data-url="{{ e($visit['url']) }}"
                >
                    <a class="m-action-main" href="{{ $visit['url'] }}">
                        <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                        <span>
                            <strong>{{ $visit['visitor'] }}</strong>
                            <small>{{ $visit['company'] }} · {{ $visit['time'] }} {{ $visit['date'] }}</small>
                        </span>
                    </a>
                    <p>{{ $visit['purpose'] ?: 'Chưa có mục đích chuyến thăm.' }}</p>
                    <div class="m-approval-meta">
                        <span class="m-approval-chip"><i class="bi bi-calendar2-check"></i>{{ $visit['code'] }}</span>
                        <span>{{ $visit['host'] }}</span>
                    </div>
                    <div class="m-inline-actions">
                        <form action="{{ route('admin.approvals.approve', $visit['id']) }}" method="post" data-approval-form data-approval-action="approved">
                            @csrf
                            <button class="m-mini-btn success" type="submit">Duyệt</button>
                        </form>
                        <form action="{{ route('admin.approvals.reject', $visit['id']) }}" method="post" data-approval-form data-approval-action="rejected">
                            @csrf
                            <input type="hidden" name="reason" value="Host từ chối yêu cầu tiếp khách.">
                            <button class="m-mini-btn danger" type="submit">Từ chối</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="m-empty" data-approval-empty="pending" @if ($pendingTotal > 0) hidden @endif>
            <i class="bi bi-patch-check"></i>
            <span>Không có yêu cầu chờ duyệt.</span>
        </div>

        <div class="m-more-wrap">
            <button class="m-more-btn" type="button" data-load-more="pending">Tải thêm</button>
        </div>
    </section>

    <section class="m-section m-approval-panel" data-approval-panel="approved" hidden>
        <div class="m-section-head">
            <div>
                <h2>Đã xử lý gần đây</h2>
                <span>Các lịch đã được duyệt gần nhất.</span>
            </div>
        </div>

        <div class="m-visit-list" data-approval-list="approved">
            @foreach ($approvedVisits as $visit)
                <a class="m-visit {{ $loop->index >= $pageSize ? 'is-hidden' : '' }}" href="{{ $visit['url'] }}" data-approval-card>
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-visit-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['code'] }} · {{ $visit['host'] }}</small>
                    </span>
                    <span class="m-visit-side">
                        <strong>{{ $visit['time'] }}</strong>
                        <small>Đã duyệt</small>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="m-empty" data-approval-empty="approved" @if ($approvedTotal > 0) hidden @endif>
            <i class="bi bi-clock-history"></i>
            <span>Chưa có lịch đã duyệt.</span>
        </div>

        <div class="m-more-wrap">
            <button class="m-more-btn" type="button" data-load-more="approved">Xem thêm</button>
        </div>
    </section>

    <section class="m-section m-approval-panel" data-approval-panel="rejected" hidden>
        <div class="m-section-head">
            <div>
                <h2>Từ chối gần đây</h2>
                <span>Các lịch bị từ chối gần nhất.</span>
            </div>
        </div>

        <div class="m-visit-list" data-approval-list="rejected">
            @foreach ($rejectedVisits as $visit)
                <a class="m-visit {{ $loop->index >= $pageSize ? 'is-hidden' : '' }}" href="{{ $visit['url'] }}" data-approval-card>
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-visit-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['code'] }} · {{ $visit['host'] }}</small>
                    </span>
                    <span class="m-visit-side">
                        <strong>{{ $visit['time'] }}</strong>
                        <small>Từ chối</small>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="m-empty" data-approval-empty="rejected" @if ($rejectedTotal > 0) hidden @endif>
            <i class="bi bi-x-circle"></i>
            <span>Chưa có lịch bị từ chối.</span>
        </div>

        <div class="m-more-wrap">
            <button class="m-more-btn" type="button" data-load-more="rejected">Xem thêm</button>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const pageSize = 5;
            const tabs = Array.from(document.querySelectorAll('[data-approval-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-approval-panel]'));
            const toast = document.querySelector('[data-approval-toast]');

            const setTab = (name) => {
                tabs.forEach((tab) => tab.classList.toggle('is-active', tab.dataset.approvalTab === name));
                panels.forEach((panel) => {
                    panel.hidden = panel.dataset.approvalPanel !== name;
                });
            };

            const cardsOf = (name) => Array.from(document.querySelectorAll(`[data-approval-list="${name}"] [data-approval-card]`));

            const updateMoreButton = (name) => {
                const button = document.querySelector(`[data-load-more="${name}"]`);
                if (!button) return;

                const hiddenCount = cardsOf(name).filter((card) => card.classList.contains('is-hidden')).length;
                button.hidden = hiddenCount === 0;
                button.textContent = name === 'pending'
                    ? `Tải thêm${hiddenCount ? ` (${hiddenCount})` : ''}`
                    : `Xem thêm${hiddenCount ? ` (${hiddenCount})` : ''}`;
            };

            const updateEmpty = (name) => {
                const empty = document.querySelector(`[data-approval-empty="${name}"]`);
                if (empty) empty.hidden = cardsOf(name).length > 0;
            };

            const updateCount = (selector, value) => {
                document.querySelectorAll(selector).forEach((item) => {
                    item.textContent = String(value);
                });
            };

            const showToast = (message, isDanger = false) => {
                if (!toast) return;
                toast.hidden = false;
                toast.classList.toggle('danger', isDanger);
                const icon = toast.querySelector('i');
                const text = toast.querySelector('span');
                if (icon) icon.className = isDanger ? 'bi bi-exclamation-triangle' : 'bi bi-check-circle';
                if (text) text.textContent = message;
                window.clearTimeout(showToast.timer);
                showToast.timer = window.setTimeout(() => {
                    toast.hidden = true;
                }, isDanger ? 12000 : 5200);
            };

            const normalizeList = (name) => {
                cardsOf(name).forEach((card, index) => {
                    card.classList.toggle('is-hidden', index >= pageSize);
                });
                updateMoreButton(name);
                updateEmpty(name);
            };

            const createDecisionCard = (visit, fallbackCard, status) => {
                const data = {
                    url: visit?.url || fallbackCard.dataset.url || '#',
                    visitor: visit?.visitor || fallbackCard.dataset.visitor || 'Khách',
                    code: visit?.code || fallbackCard.dataset.code || '',
                    host: visit?.host || fallbackCard.dataset.host || '',
                    time: visit?.time || fallbackCard.dataset.time || '',
                    status: visit?.status || status,
                };
                const statusLabel = data.status === 'approved' ? 'Đã duyệt' : 'Từ chối';

                const card = document.createElement('a');
                card.className = 'm-visit';
                card.href = data.url;
                card.dataset.approvalCard = '';
                card.innerHTML = `
                    <span class="m-avatar">${data.visitor.trim().charAt(0).toUpperCase() || 'K'}</span>
                    <span class="m-visit-main">
                        <strong></strong>
                        <small></small>
                    </span>
                    <span class="m-visit-side">
                        <strong></strong>
                        <small></small>
                    </span>
                `;
                card.querySelector('.m-visit-main strong').textContent = data.visitor;
                card.querySelector('.m-visit-main small').textContent = `${data.code} · ${data.host}`;
                card.querySelector('.m-visit-side strong').textContent = data.time;
                card.querySelector('.m-visit-side small').textContent = statusLabel;

                return card;
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => setTab(tab.dataset.approvalTab));
            });

            document.querySelectorAll('[data-load-more]').forEach((button) => {
                button.addEventListener('click', () => {
                    const name = button.dataset.loadMore;
                    const hiddenCards = cardsOf(name).filter((card) => card.classList.contains('is-hidden'));
                    hiddenCards.slice(0, pageSize).forEach((card) => card.classList.remove('is-hidden'));
                    updateMoreButton(name);
                });
            });

            document.querySelectorAll('[data-approval-form]').forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const card = form.closest('[data-approval-card]');
                    const action = form.dataset.approvalAction;
                    const targetListName = action === 'approved' ? 'approved' : 'rejected';
                    const button = form.querySelector('button[type="submit"]');
                    const formData = new FormData(form);

                    if (button) button.disabled = true;

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(payload.message || 'Chưa xử lý được yêu cầu.');
                        }

                        const targetList = document.querySelector(`[data-approval-list="${targetListName}"]`);
                        if (targetList && card) {
                            targetList.prepend(createDecisionCard(payload.visit, card, action));
                        }

                        if (card) {
                            card.classList.add('is-removing');
                            window.setTimeout(() => {
                                card.remove();
                                updateCount('[data-pending-count]', cardsOf('pending').length);
                                updateCount('[data-approved-count]', cardsOf('approved').length);
                                updateCount('[data-rejected-count]', cardsOf('rejected').length);
                                updateEmpty('pending');
                                updateMoreButton('pending');
                                normalizeList(targetListName);
                            }, 180);
                        }

                        showToast(payload.message || (action === 'approved' ? 'Đã duyệt lịch.' : 'Đã từ chối lịch.'));
                    } catch (error) {
                        showToast(error.message, true);
                        if (button) button.disabled = false;
                    }
                });
            });

            updateMoreButton('pending');
            updateMoreButton('approved');
            updateMoreButton('rejected');
        })();
    </script>
@endpush
