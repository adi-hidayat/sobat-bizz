@use('App\Enums\LeadStatus')
@use('App\Filament\Resources\Leads\LeadResource')

<x-filament-panels::page>
    <style>
        .lead-board {
            --lb-bg: #f3f4f6;
            --lb-column: #f9fafb;
            --lb-card: #ffffff;
            --lb-border: #e5e7eb;
            --lb-text: #111827;
            --lb-muted: #6b7280;
            --lb-primary: #059669;
        }

        .dark .lead-board {
            --lb-bg: #0b1120;
            --lb-column: #111827;
            --lb-card: #1f2937;
            --lb-border: #374151;
            --lb-text: #f3f4f6;
            --lb-muted: #9ca3af;
            --lb-primary: #34d399;
        }

        .lead-board {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            color: var(--lb-text);
        }

        .lead-board-toolbar {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--lb-card);
            border: 1px solid var(--lb-border);
            border-radius: 0.75rem;
            container-type: inline-size;
        }

        .lead-board-toolbar-filters {
            flex: 1 1 auto;
            min-width: 0;
        }

        .lead-board-card-fields-toggle {
            flex: none;
            margin-top: 0;
        }

        .lead-card-fields-action-hidden {
            display: none !important;
        }

        .lead-board-toolbar .fi-sc {
            display: flex !important;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 0.5rem;
            grid-template-columns: none !important;
        }

        .lead-board-toolbar .fi-sc > .fi-grid-col {
            flex: 1 1 10rem;
            min-width: 10rem;
        }

        .lead-board-toolbar .fi-sc > .fi-grid-col:first-child {
            flex: 2 1 16rem;
            min-width: 10rem;
        }

        @container (max-width: 1200px) {
            .lead-board-toolbar .fi-sc > .fi-grid-col:first-child {
                flex-basis: 100%;
            }
        }

        .lead-board-toolbar .fi-input,
        .lead-board-toolbar .fi-select-input-btn,
        .lead-board-toolbar .fi-select-input-placeholder,
        .lead-board-toolbar .fi-select-input-value-label,
        .lead-board-toolbar .fi-select-input-value-ctn {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lead-board-toolbar .fi-select-input-value-ctn {
            min-width: 0;
            line-height: 1;
        }

        .lead-board-toolbar .fi-select-input-btn {
            height: 2.25rem;
            min-height: 2.25rem;
            line-height: 1;
        }

        .lead-board-toolbar .fi-select-input-btn span {
            line-height: 1;
        }

        .lead-board-toolbar .fi-select-input-btn img {
            display: block;
        }

        .lead-board-columns {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.75rem;
            align-items: stretch;
            height: calc(100dvh - 19.5rem);
            min-height: 18rem;
        }

        @media (max-width: 1400px) {
            .lead-board-columns {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .lead-board-columns {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .lead-board-columns {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .lead-board-column {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
            min-height: 0;
            padding: 0;
            border: 1px solid var(--lb-border);
            border-radius: 0.75rem;
            background: var(--lb-bg);
            overflow: hidden;
            transition: outline-color 120ms ease, background-color 120ms ease;
            outline: 2px solid transparent;
        }

        .lead-board-column.is-drag-over {
            outline-color: var(--lb-status, var(--lb-primary));
            background: color-mix(in srgb, var(--lb-status, var(--lb-primary)) 8%, var(--lb-bg));
        }

        .lead-board-column-header {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            padding: 0.625rem 0.75rem;
            background: var(--lb-card);
            border-bottom: 1px solid var(--lb-border);
        }

        .lead-board-column-title {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .lead-board-count {
            margin-left: auto;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--lb-muted);
        }

        .lead-board-column-value {
            font-size: 0.6875rem;
            color: var(--lb-muted);
        }

        .lead-board-cards {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
            min-height: 3rem;
            overflow-y: auto;
            padding: 0.625rem;
            padding-right: 0.5rem;
            scrollbar-width: thin;
        }

        .lead-board-card {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            padding: 0.625rem;
            border: 1px solid var(--lb-border);
            border-radius: 0.5rem;
            background: var(--lb-card);
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
            cursor: grab;
        }

        .lead-board-card.is-dragging {
            opacity: 0.45;
            outline: 2px solid var(--lb-primary);
            outline-offset: -2px;
            cursor: grabbing;
            animation: lead-board-drag-flash 1.1s ease-in-out infinite;
        }

        @keyframes lead-board-drag-flash {
            0%,
            100% {
                outline-color: var(--lb-status, var(--lb-primary));
            }

            50% {
                outline-color: color-mix(in srgb, var(--lb-status, var(--lb-primary)) 15%, transparent);
            }
        }

        .lead-board-drop-slot {
            flex: none;
            border: none;
            outline: 2px solid var(--lb-status, var(--lb-primary));
            outline-offset: -2px;
            border-radius: 0.5rem;
            background: color-mix(in srgb, var(--lb-status, var(--lb-primary)) 8%, transparent);
            pointer-events: none;
            animation: lead-board-drag-flash 1.1s ease-in-out infinite;
        }

        .lead-board-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.375rem;
        }

        .lead-board-card-title {
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--lb-text);
            text-decoration: none;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .lead-board-card-title:hover {
            text-decoration: underline;
        }

        .lead-board-card-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.6875rem;
            color: var(--lb-muted);
        }

        .lead-board-card-value {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--lb-primary);
        }

        .lead-board-card-footer {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
            padding-top: 0.375rem;
            font-size: 0.6875rem;
            color: var(--lb-muted);
        }

        .lead-board-card-people {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 0;
        }

        .lead-board-person {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.125rem;
            min-width: 0;
        }

        .lead-board-person-label {
            flex: none;
            font-size: 0.6875rem;
            font-weight: 400;
            color: var(--lb-muted);
        }

        .lead-board-person-value {
            color: var(--lb-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lead-board-follow-up {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.125rem;
            flex: none;
        }

        .lead-board-card-footer .is-overdue {
            color: #ef4444;
            font-weight: 600;
        }

        .lead-board-team {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .lead-board-team-avatar-wrap {
            display: inline-flex;
        }

        .lead-board-tooltip {
            position: fixed;
            z-index: 9999;
            transform: translate(-50%, -100%);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            background: rgb(17 24 39 / 0.95);
            color: #fff;
            font-size: 0.625rem;
            line-height: 1.2;
            white-space: nowrap;
            pointer-events: none;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.2);
        }

        .lead-board-team-avatar {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 1px solid var(--lb-card);
        }

        .lead-board-team-more {
            font-size: 0.625rem;
            color: var(--lb-muted);
        }

        .lead-board-move {
            display: none;
            width: 100%;
            margin-top: 0.125rem;
            padding: 0.25rem;
            border: 1px solid var(--lb-border);
            border-radius: 0.375rem;
            background: transparent;
            color: var(--lb-text);
            font-size: 0.6875rem;
        }

        @media (max-width: 768px), (hover: none) {
            .lead-board-move {
                display: block;
            }
        }

        .lead-board-empty {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            min-height: 6rem;
            padding: 0.5rem;
            border: 1px solid var(--lb-border);
            border-radius: 0.5rem;
            font-size: 0.6875rem;
            color: var(--lb-muted);
            text-align: center;
        }

        .lead-board-load-more {
            margin: 0;
            padding: 0.25rem;
            font-size: 0.625rem;
            color: var(--lb-muted);
            text-align: center;
        }

        .lead-board-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgb(0 0 0 / 0.5);
        }

        .lead-board-modal {
            width: 100%;
            max-width: 28rem;
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
            padding: 1rem;
            border: 1px solid var(--lb-border);
            border-radius: 0.75rem;
            background: var(--lb-card);
            color: var(--lb-text);
            box-shadow: 0 20px 40px rgb(0 0 0 / 0.2);
        }

        .lead-board-modal h3 {
            font-size: 0.9375rem;
            font-weight: 600;
        }

        .lead-board-modal p {
            font-size: 0.75rem;
            color: var(--lb-muted);
        }

        .lead-board-textarea {
            width: 100%;
            border: 1px solid var(--lb-border);
            border-radius: 0.5rem;
            background: var(--lb-column);
            color: var(--lb-text);
            padding: 0.5rem;
            font-size: 0.8125rem;
        }

        .lead-board-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .lead-board-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid transparent;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            color: #fff;
        }

        .lead-board-btn-secondary {
            background: var(--lb-column);
            border-color: var(--lb-border);
            color: var(--lb-text);
        }

        .lead-board-btn-danger {
            background: #dc2626;
        }
    </style>

    <div
        class="lead-board"
        x-data="{
            draggingId: null,
            dragTarget: null,
            dropIndex: null,
            dropSlot: null,
            dragGhost: null,
            dragOffsetX: 0,
            dragOffsetY: 0,
            dragLastX: 0,
            dragDirection: 0,
            dragMoveHandler: null,
            loadingColumns: {},
            pending: null,
            lostReason: '',
            tooltip: { show: false, text: '', x: 0, y: 0 },
            showTooltip(event, text) {
                const rect = event.currentTarget.getBoundingClientRect();

                this.tooltip = {
                    show: true,
                    text: text,
                    x: rect.left + (rect.width / 2),
                    y: rect.top - 8,
                };
            },
            hideTooltip() {
                this.tooltip = { ...this.tooltip, show: false };
            },
            init() {
                document.querySelectorAll('.lead-board-drop-slot').forEach((slot) => slot.remove());

                this.dropSlot = document.createElement('div');
                this.dropSlot.className = 'lead-board-drop-slot';
                this.dropSlot.style.display = 'none';
            },
            startDrag(id, event) {
                this.draggingId = id;
                this.dropIndex = null;

                if (! event?.dataTransfer) { return }

                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', String(id));

                const card = event.currentTarget;
                const rect = card.getBoundingClientRect();
                const ghost = card.cloneNode(true);

                ghost.setAttribute('x-ignore', '');
                ghost.classList.remove('is-dragging');

                const boardStyles = getComputedStyle(card.closest('.lead-board'));

                ['--lb-bg', '--lb-column', '--lb-card', '--lb-border', '--lb-text', '--lb-muted', '--lb-primary'].forEach((name) => {
                    const value = boardStyles.getPropertyValue(name);

                    if (value) {
                        ghost.style.setProperty(name, value);
                    }
                });

                const cardStyles = getComputedStyle(card);

                ghost.style.background = cardStyles.backgroundColor;
                ghost.style.borderColor = cardStyles.borderColor;
                ghost.style.position = 'fixed';
                ghost.style.left = rect.left + 'px';
                ghost.style.top = rect.top + 'px';
                ghost.style.width = rect.width + 'px';
                ghost.style.margin = '0';
                ghost.style.pointerEvents = 'none';
                ghost.style.zIndex = '9999';
                ghost.style.opacity = '1';
                ghost.style.transform = 'rotate(0deg) scale(1.01)';
                ghost.style.transformOrigin = 'center center';
                ghost.style.transition = 'transform 300ms cubic-bezier(0.22, 1, 0.36, 1)';
                ghost.style.willChange = 'transform';
                ghost.style.boxShadow = '0 12px 28px rgb(0 0 0 / 0.28)';

                document.body.appendChild(ghost);

                this.dragGhost = ghost;
                this.dragOffsetX = event.clientX - rect.left;
                this.dragOffsetY = event.clientY - rect.top;
                this.dragLastX = event.clientX;
                this.dragDirection = 0;

                this.dragMoveHandler = (moveEvent) => {
                    if (! moveEvent.clientX && ! moveEvent.clientY) { return }

                    this.moveGhost(moveEvent.clientX, moveEvent.clientY);
                };

                window.addEventListener('drag', this.dragMoveHandler);

                const transparentPixel = new Image();
                transparentPixel.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
                event.dataTransfer.setDragImage(transparentPixel, 0, 0);
            },
            moveGhost(x, y) {
                if (! this.dragGhost) { return }

                const deltaX = x - this.dragLastX;
                this.dragLastX = x;

                this.dragGhost.style.left = (x - this.dragOffsetX) + 'px';
                this.dragGhost.style.top = (y - this.dragOffsetY) + 'px';

                if (Math.abs(deltaX) < 3) { return }

                const direction = deltaX > 0 ? 1 : -1;

                if (direction === this.dragDirection) { return }

                this.dragDirection = direction;
                this.dragGhost.style.transform = 'rotate(' + (direction * 5) + 'deg) scale(1.01)';
            },
            cleanupDragGhost() {
                if (this.dragMoveHandler) {
                    window.removeEventListener('drag', this.dragMoveHandler);
                    this.dragMoveHandler = null;
                }

                this.dragGhost?.remove();
                this.dragGhost = null;
            },
            onColumnScroll(status, event, hasMore, livewire) {
                if (! hasMore) { return }

                const element = event.currentTarget;

                if ((element.scrollTop + element.clientHeight) < (element.scrollHeight - 32)) { return }

                if (this.loadingColumns[status]) { return }

                this.loadingColumns[status] = true;

                const finish = () => {
                    this.loadingColumns[status] = false;
                };

                const request = livewire.loadMoreLeads(status);

                if (request?.finally) {
                    request.finally(finish);
                } else {
                    finish();
                }
            },
            endDrag() {
                this.cleanupDragGhost();
                this.draggingId = null;
                this.dragTarget = null;
                this.dropIndex = null;
                this.hideDropSlot();
            },
            hideDropSlot() {
                if (! this.dropSlot) { return }

                this.dropSlot.style.display = 'none';
                this.dropSlot.remove();
            },
            dragOver(status, event) {
                if (! this.draggingId) { return }

                if ((this.dragTarget !== status) && this.dragGhost) {
                    this.dragDirection = 0;
                    this.dragGhost.style.transform = 'rotate(0deg) scale(1.01)';
                }

                this.dragTarget = status;

                const container = event.currentTarget.querySelector('.lead-board-cards');

                if (! container) { return }

                const cards = [...container.querySelectorAll('.lead-board-card')].filter((card) => ! card.classList.contains('is-dragging'));

                let index = cards.length;

                for (let i = 0; i < cards.length; i++) {
                    const rect = cards[i].getBoundingClientRect();

                    if (event.clientY < rect.top + (rect.height / 2)) {
                        index = i;
                        break;
                    }
                }

                this.dropIndex = index;

                const draggedCard = document.querySelector('.lead-board-card.is-dragging');

                this.dropSlot.style.height = (draggedCard?.offsetHeight ?? 64) + 'px';
                this.dropSlot.style.display = 'block';
                container.insertBefore(this.dropSlot, cards[index] ?? null);
            },
            dragLeave(status, event) {
                if (! this.draggingId) { return }

                if (event.relatedTarget && event.currentTarget.contains(event.relatedTarget)) { return }

                this.dragTarget = null;
                this.dropIndex = null;
                this.hideDropSlot();
            },
            drop(status, livewire) {
                if (! this.draggingId) { return }

                const id = this.draggingId;
                const position = this.dropIndex;

                this.cleanupDragGhost();
                this.draggingId = null;
                this.dragTarget = null;
                this.dropIndex = null;
                this.hideDropSlot();

                if (status === 'lost') {
                    this.pending = { id: id, status: status, position: position };
                    this.lostReason = '';

                    return;
                }

                livewire.moveLead(id, status, null, position);
            },
        }"
    >
        <div class="lead-board-toolbar">
            <div class="lead-board-toolbar-filters">
                {{ $this->form }}
            </div>

            <x-filament::button
                :tooltip="__('resources/lead.board.card_fields')"
                icon="heroicon-o-view-columns"
                color="gray"
                wire:click="openCardFields"
                class="lead-board-card-fields-toggle"
            />
        </div>

        @php
            $board = $this->getBoard();
        @endphp

        <div class="lead-board-columns">
            @foreach ($board['columns'] as $column)
                @php
                    $status = $column['status'];
                    $statusValue = $status->value;
                @endphp

                <section
                    wire:key="lead-board-column-{{ $statusValue }}"
                    class="lead-board-column"
                    style="--lb-status: {{ $this->statusColor($status) }};"
                    x-bind:class="dragTarget === '{{ $statusValue }}' ? 'is-drag-over' : ''"
                    x-on:dragover.prevent="dragOver('{{ $statusValue }}', $event)"
                    x-on:dragleave="dragLeave('{{ $statusValue }}', $event)"
                    x-on:drop.prevent="drop('{{ $statusValue }}', $wire)"
                >
                    <header class="lead-board-column-header">
                        <div class="lead-board-column-title">
                            {{ $status->getLabel() }}
                            <span class="lead-board-count">{{ $column['count'] }}</span>
                        </div>
                        <div class="lead-board-column-value">
                            Rp {{ number_format($column['value'], 0, ',', '.') }}
                        </div>
                    </header>

                    <div
                        class="lead-board-cards"
                        x-on:scroll.passive="onColumnScroll('{{ $statusValue }}', $event, {{ $column['has_more'] ? 'true' : 'false' }}, $wire)"
                    >
                        @forelse ($column['leads'] as $lead)
                            <article
                                wire:key="lead-card-{{ $lead->getKey() }}"
                                class="lead-board-card"
                                draggable="{{ $lead->status === LeadStatus::Converted ? 'false' : 'true' }}"
                                style="
                                    --lb-accent: {{ $this->statusColor($lead->status) }};
                                    background: {{ $lead->status === LeadStatus::New ? 'var(--lb-card)' : 'color-mix(in srgb, var(--lb-accent) 12%, var(--lb-card))' }};
                                    border-color: {{ $lead->status === LeadStatus::New ? 'var(--lb-border)' : 'color-mix(in srgb, var(--lb-accent) 30%, var(--lb-border))' }};
                                "
                                x-bind:class="draggingId === {{ $lead->getKey() }} ? 'is-dragging' : ''"
                                x-on:dragstart="startDrag({{ $lead->getKey() }}, $event)"
                                x-on:dragend="endDrag()"
                            >
                                @if ($this->showCardField('name') || ($this->showCardField('rating') && $lead->rating))
                                    <div class="lead-board-card-top">
                                        @if ($this->showCardField('name'))
                                            <a
                                                href="{{ LeadResource::getUrl('view', ['record' => $lead]) }}"
                                                class="lead-board-card-title"
                                            >
                                                {{ $lead->display_name }}
                                            </a>
                                        @endif

                                        @if ($this->showCardField('rating') && $lead->rating)
                                            <x-filament::badge
                                                size="sm"
                                                style="background-color: {{ $lead->rating->color }}; color: {{ $lead->rating->text_color ?: '#ffffff' }};"
                                            >
                                                {{ $lead->rating->name }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                @endif

                                @if ($this->showCardField('type') || ($this->showCardField('source') && $lead->leadSource))
                                    <div class="lead-board-card-meta">
                                        @if ($this->showCardField('type'))
                                            <x-filament::badge color="primary" size="sm">
                                                {{ $lead->lead_type->getLabel() }}
                                            </x-filament::badge>
                                        @endif

                                        @if ($this->showCardField('source') && $lead->leadSource)
                                            <x-filament::badge color="info" size="sm">
                                                {{ $lead->leadSource->name }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                @endif

                                @if ($this->showCardField('value') && (float) $lead->estimated_value > 0)
                                    <div class="lead-board-card-value">
                                        ~Rp {{ number_format((float) $lead->estimated_value, 0, ',', '.') }}
                                    </div>
                                @endif

                                @php
                                    $showOwner = $this->showCardField('owner');
                                    $showTeam = $this->showCardField('team');
                                    $showFollowUp = $this->showCardField('follow_up') && $lead->follow_up_date;
                                @endphp

                                @if ($showOwner || $showTeam || $showFollowUp)
                                    <div class="lead-board-card-footer">
                                        @if ($showOwner || $showTeam)
                                            <div class="lead-board-card-people">
                                                @if ($showOwner)
                                                    <div class="lead-board-person">
                                                        <span class="lead-board-person-label">
                                                            {{ __('resources/lead.fields.sales_person') }}
                                                        </span>

                                                        @if ($lead->assignedSalesPerson)
                                                            <div class="lead-board-team">
                                                                <span
                                                                    class="lead-board-team-avatar-wrap"
                                                                    data-name="{{ $lead->assignedSalesPerson->name }}"
                                                                    x-on:mouseenter="showTooltip($event, $event.currentTarget.dataset.name)"
                                                                    x-on:mouseleave="hideTooltip()"
                                                                >
                                                                    <img
                                                                        src="{{ $lead->assignedSalesPerson->avatar_url }}"
                                                                        alt="{{ $lead->assignedSalesPerson->name }}"
                                                                        class="lead-board-team-avatar"
                                                                    />
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="lead-board-person-value">-</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($showTeam)
                                                    <div class="lead-board-person">
                                                        <span class="lead-board-person-label">
                                                            {{ __('resources/lead.fields.sales_team') }}
                                                        </span>

                                                        @if ($lead->salesTeam->isNotEmpty())
                                                            <div class="lead-board-team">
                                                                @foreach ($lead->salesTeam->take(3) as $member)
                                                                    <span
                                                                        class="lead-board-team-avatar-wrap"
                                                                        data-name="{{ $member->name }}"
                                                                        x-on:mouseenter="showTooltip($event, $event.currentTarget.dataset.name)"
                                                                        x-on:mouseleave="hideTooltip()"
                                                                    >
                                                                        <img
                                                                            src="{{ $member->avatar_url }}"
                                                                            alt="{{ $member->name }}"
                                                                            class="lead-board-team-avatar"
                                                                        />
                                                                    </span>
                                                                @endforeach

                                                                @if ($lead->salesTeam->count() > 3)
                                                                    <span
                                                                        class="lead-board-team-more"
                                                                        data-name="{{ $lead->salesTeam->skip(3)->pluck('name')->implode(', ') }}"
                                                                        x-on:mouseenter="showTooltip($event, $event.currentTarget.dataset.name)"
                                                                        x-on:mouseleave="hideTooltip()"
                                                                    >
                                                                        +{{ $lead->salesTeam->count() - 3 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="lead-board-person-value">{{ __('common.placeholders.none') }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($showFollowUp)
                                            <div class="lead-board-person lead-board-follow-up">
                                                <span class="lead-board-person-label">
                                                    {{ __('resources/lead.fields.follow_up') }}
                                                </span>
                                                <span class="lead-board-person-value {{ $lead->follow_up_date->isPast() ? 'is-overdue' : '' }}">
                                                    {{ $lead->follow_up_date->format('d M Y') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($lead->status !== LeadStatus::Converted)
                                    <select
                                        class="lead-board-move"
                                        x-on:change="
                                            const status = $event.target.value;
                                            $event.target.value = '';

                                            if (! status) { return }

                                            if (status === 'lost') {
                                                pending = { id: {{ $lead->getKey() }}, status: status };
                                                lostReason = '';
                                            } else {
                                                $wire.moveLead({{ $lead->getKey() }}, status);
                                            }
                                        "
                                    >
                                        <option value="">{{ __('resources/lead.board.move') }}</option>
                                        @foreach ($this->getMovableStatuses() as $movableStatus)
                                            @if ($movableStatus !== $lead->status)
                                                <option value="{{ $movableStatus->value }}">{{ $movableStatus->getLabel() }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </article>
                        @empty
                            <p class="lead-board-empty">{{ __('resources/lead.board.empty_column') }}</p>
                        @endforelse

                        @if ($column['has_more'])
                            <p class="lead-board-load-more">
                                {{ __('resources/lead.board.load_more', [
                                    'loaded' => $column['loaded'],
                                    'total' => $column['count'],
                                ]) }}
                            </p>
                        @endif
                    </div>
                </section>
            @endforeach
        </div>

        <template x-if="pending">
            <div class="lead-board-modal-backdrop" x-on:click.self="pending = null">
                <div class="lead-board-modal">
                    <h3>{{ __('resources/lead.board.lost_reason_title') }}</h3>
                    <p>{{ __('resources/lead.board.lost_reason_help') }}</p>

                    <textarea
                        class="lead-board-textarea"
                        rows="4"
                        x-model="lostReason"
                        placeholder="{{ __('resources/lead.board.lost_reason_placeholder') }}"
                    ></textarea>

                    <div class="lead-board-modal-actions">
                        <button type="button" class="lead-board-btn lead-board-btn-secondary" x-on:click="pending = null">
                            {{ __('common.actions.cancel') }}
                        </button>
                        <button
                            type="button"
                            class="lead-board-btn lead-board-btn-danger"
                            x-on:click="$wire.moveLead(pending.id, pending.status, lostReason, pending.position); pending = null; lostReason = '';"
                        >
                            {{ __('common.actions.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div
                x-show="tooltip.show"
                x-cloak
                class="lead-board-tooltip"
                x-bind:style="'left: ' + tooltip.x + 'px; top: ' + tooltip.y + 'px;'"
                x-text="tooltip.text"
            ></div>
        </template>
    </div>
</x-filament-panels::page>
