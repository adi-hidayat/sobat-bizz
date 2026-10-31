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
            display: block;
            padding: 0.75rem;
            background: var(--lb-card);
            border: 1px solid var(--lb-border);
            border-radius: 0.75rem;
            container-type: inline-size;
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
            grid-template-columns: repeat(6, minmax(0, 1fr));
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
            outline: 2px dashed transparent;
        }

        .lead-board-column.is-drag-over {
            outline-color: var(--lb-primary);
            background: color-mix(in srgb, var(--lb-primary) 8%, var(--lb-bg));
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
            opacity: 0.35;
        }

        .lead-board-drop-slot {
            flex: none;
            border: 2px dashed var(--lb-primary);
            border-radius: 0.5rem;
            background: color-mix(in srgb, var(--lb-primary) 8%, transparent);
            pointer-events: none;
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

        .lead-board-rating {
            flex: none;
            padding: 0.0625rem 0.375rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 600;
            color: #fff;
            background: var(--lb-rating, #9ca3af);
        }

        .lead-board-card-meta {
            display: flex;
            flex-wrap: wrap;
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
            align-items: center;
            justify-content: space-between;
            gap: 0.375rem;
            font-size: 0.6875rem;
            color: var(--lb-muted);
        }

        .lead-board-card-footer .is-overdue {
            color: #ef4444;
            font-weight: 600;
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
            border: 1px dashed var(--lb-border);
            border-radius: 0.5rem;
            font-size: 0.6875rem;
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
            pending: null,
            lostReason: '',
            init() {
                document.querySelectorAll('.lead-board-drop-slot').forEach((slot) => slot.remove());

                this.dropSlot = document.createElement('div');
                this.dropSlot.className = 'lead-board-drop-slot';
                this.dropSlot.style.display = 'none';
            },
            startDrag(id, event) {
                this.draggingId = id;
                this.dropIndex = null;

                if (event?.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', String(id));
                }
            },
            endDrag() {
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

                this.draggingId = null;
                this.dragTarget = null;
                this.dropIndex = null;
                this.hideDropSlot();

                if (status === 'lost' || status === 'unqualified') {
                    this.pending = { id: id, status: status, position: position };
                    this.lostReason = '';

                    return;
                }

                livewire.moveLead(id, status, null, position);
            },
        }"
    >
        <div class="lead-board-toolbar">
            {{ $this->form }}
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

                    <div class="lead-board-cards">
                        @forelse ($column['leads'] as $lead)
                            <article
                                wire:key="lead-card-{{ $lead->getKey() }}"
                                class="lead-board-card"
                                draggable="{{ $lead->status === LeadStatus::Converted ? 'false' : 'true' }}"
                                style="
                                    --lb-accent: {{ $this->statusColor($lead->status) }};
                                    background: color-mix(in srgb, var(--lb-accent) 12%, var(--lb-card));
                                    border-color: color-mix(in srgb, var(--lb-accent) 30%, var(--lb-border));
                                "
                                x-bind:class="draggingId === {{ $lead->getKey() }} ? 'is-dragging' : ''"
                                x-on:dragstart="startDrag({{ $lead->getKey() }}, $event)"
                                x-on:dragend="endDrag()"
                            >
                                <div class="lead-board-card-top">
                                    <a
                                        href="{{ LeadResource::getUrl('view', ['record' => $lead]) }}"
                                        class="lead-board-card-title"
                                    >
                                        {{ $lead->display_name }}
                                    </a>

                                    @if ($lead->rating)
                                        <span
                                            class="lead-board-rating"
                                            style="--lb-rating: {{ $this->ratingColor($lead->rating) }}"
                                        >
                                            {{ $lead->rating->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="lead-board-card-meta">
                                    <span>{{ $lead->lead_type->getLabel() }}</span>

                                    @if ($lead->leadSource)
                                        <span>&middot; {{ $lead->leadSource->name }}</span>
                                    @endif
                                </div>

                                @if ((float) $lead->estimated_value > 0)
                                    <div class="lead-board-card-value">
                                        Rp {{ number_format((float) $lead->estimated_value, 0, ',', '.') }}
                                    </div>
                                @endif

                                <div class="lead-board-card-footer">
                                    <span>{{ $lead->assignedSalesPerson?->name ?? __('resources/lead.board.unassigned') }}</span>

                                    @if ($lead->follow_up_date)
                                        <span class="{{ $lead->follow_up_date->isPast() ? 'is-overdue' : '' }}">
                                            {{ $lead->follow_up_date->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>

                                @if ($lead->status !== LeadStatus::Converted)
                                    <select
                                        class="lead-board-move"
                                        x-on:change="
                                            const status = $event.target.value;
                                            $event.target.value = '';

                                            if (! status) { return }

                                            if (status === 'lost' || status === 'unqualified') {
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
    </div>
</x-filament-panels::page>
