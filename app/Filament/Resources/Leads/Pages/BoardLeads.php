<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Actions\MoveLeadStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Filament\Resources\Leads\Actions\LeadViewModeActions;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Support\UserAvatar;
use App\Models\Lead;
use App\Models\LeadRating;
use App\Models\LeadSource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Attributes\Url;

class BoardLeads extends Page
{
    /**
     * Informasi kartu yang tampil secara default.
     *
     * @var list<string>
     */
    private const DEFAULT_CARD_FIELDS = ['name', 'type', 'source', 'value'];

    /**
     * Jumlah kartu yang dimuat per kolom sebelum memuat lagi.
     */
    private const CARD_PAGE_SIZE = 25;

    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.board-leads';

    /**
     * @var list<string>
     */
    public array $visibleCardFields = self::DEFAULT_CARD_FIELDS;

    /**
     * @var array<string, int>
     */
    public array $loadedCounts = [];

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $assignedTo = null;

    #[Url]
    public ?string $leadSourceId = null;

    #[Url]
    public ?string $leadRatingId = null;

    #[Url]
    public ?string $leadType = null;

    public function getTitle(): string|Htmlable
    {
        return __('resources/lead.board.title');
    }

    public function getHeaderActions(): array
    {
        return [
            LeadViewModeActions::make('board'),
            Action::make('cardFields')
                ->label(__('resources/lead.board.card_fields'))
                ->extraAttributes(['class' => 'lead-card-fields-action-hidden'])
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('gray')
                ->slideOver()
                ->modalHeading(__('resources/lead.board.card_fields'))
                ->modalDescription(__('resources/lead.board.card_fields_description'))
                ->form([
                    CheckboxList::make('fields')
                        ->label(__('resources/lead.board.card_fields'))
                        ->options(fn (): array => $this->cardFieldOptions())
                        ->default(fn (): array => $this->visibleCardFields)
                        ->columns(2),
                ])
                ->action(function (array $data): void {
                    $allowed = array_keys($this->cardFieldOptions());

                    $this->visibleCardFields = array_values(array_intersect(
                        (array) ($data['fields'] ?? []),
                        $allowed,
                    ));

                    session(['leads.card_fields' => $this->visibleCardFields]);
                }),
            Action::make('create')
                ->label(__('resources/lead.actions.create'))
                ->icon('heroicon-o-plus')
                ->url(LeadResource::getUrl('create')),
        ];
    }

    public function mount(): void
    {
        session(['leads.view_mode' => 'board']);

        $stored = session('leads.card_fields', self::DEFAULT_CARD_FIELDS);

        $this->visibleCardFields = array_values(array_intersect(
            (array) $stored,
            array_keys($this->cardFieldOptions()),
        ));
    }

    public function openCardFields(): void
    {
        $this->mountAction('cardFields');
    }

    /**
     * @return array<string, string>
     */
    public function cardFieldOptions(): array
    {
        return [
            'name' => __('resources/lead.fields.lead'),
            'type' => __('resources/lead.fields.lead_type'),
            'source' => __('resources/lead.fields.source'),
            'value' => __('resources/lead.fields.estimated_value'),
            'rating' => __('resources/lead.fields.rating'),
            'owner' => __('resources/lead.fields.sales_person'),
            'team' => __('resources/lead.fields.sales_team'),
            'follow_up' => __('resources/lead.fields.follow_up'),
        ];
    }

    public function showCardField(string $field): bool
    {
        return in_array($field, $this->visibleCardFields, true);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('search')
                    ->hiddenLabel()
                    ->placeholder(__('resources/lead.board.search_placeholder'))
                    ->suffixIcon('heroicon-o-magnifying-glass')
                    ->suffixAction(
                        Action::make('clearSearch')
                            ->label(__('resources/lead.board.clear_search'))
                            ->icon('heroicon-o-x-mark')
                            ->action(fn () => $this->search = '')
                            ->visible(fn (): bool => filled($this->search)),
                    )
                    ->live(debounce: 400)
                    ->columnSpan(['default' => 1, 'md' => 2]),
                Select::make('assignedTo')
                    ->hiddenLabel()
                    ->placeholder(__('resources/lead.board.all_sales'))
                    ->suffixIcon('heroicon-o-users')
                    ->options(fn (): array => $this->getUserOptions())
                    ->searchable()
                    ->allowHtml()
                    ->live(),
                Select::make('leadSourceId')
                    ->hiddenLabel()
                    ->placeholder(__('resources/lead.board.all_sources'))
                    ->suffixIcon('heroicon-o-megaphone')
                    ->options(fn (): array => $this->getLeadSourceOptions())
                    ->searchable()
                    ->live(),
                Select::make('leadRatingId')
                    ->hiddenLabel()
                    ->placeholder(__('resources/lead.board.all_ratings'))
                    ->suffixIcon('heroicon-o-star')
                    ->options(fn (): array => LeadRating::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->live(),
                Select::make('leadType')
                    ->hiddenLabel()
                    ->placeholder(__('resources/lead.board.all_types'))
                    ->suffixIcon('heroicon-o-building-office-2')
                    ->options(LeadType::class)
                    ->searchable()
                    ->live(),
            ])
            ->columns(['default' => 1, 'md' => 6]);
    }

    /**
     * @return array{
     *     columns: list<array{status: LeadStatus, leads: Collection<int, Lead>, count: int, loaded: int, has_more: bool, value: float}>
     * }
     */
    public function getBoard(): array
    {
        $statuses = [
            LeadStatus::New,
            LeadStatus::Contacted,
            LeadStatus::Qualified,
            LeadStatus::Converted,
            LeadStatus::Lost,
        ];

        $baseQuery = $this->query();

        $aggregates = (clone $baseQuery)
            ->selectRaw('status, count(*) as total, sum(estimated_value) as total_value')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $columns = array_map(function (LeadStatus $status) use ($baseQuery, $aggregates): array {
            $aggregate = $aggregates->get($status->value);
            $total = (int) ($aggregate->total ?? 0);
            $limit = min($total, $this->loadedCounts[$status->value] ?? self::CARD_PAGE_SIZE);

            $items = $limit > 0
                ? (clone $baseQuery)
                    ->with(['leadSource', 'assignedSalesPerson.staff', 'rating', 'salesTeam.staff'])
                    ->where('status', $status->value)
                    ->orderBy('board_position')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get()
                : collect();

            return [
                'status' => $status,
                'leads' => $items,
                'count' => $total,
                'loaded' => $items->count(),
                'has_more' => $items->count() < $total,
                'value' => (float) ($aggregate->total_value ?? 0),
            ];
        }, $statuses);

        return [
            'columns' => $columns,
        ];
    }

    public function loadMoreLeads(string $status): void
    {
        if (LeadStatus::tryFrom($status) === null) {
            return;
        }

        $this->loadedCounts[$status] = ($this->loadedCounts[$status] ?? self::CARD_PAGE_SIZE) + self::CARD_PAGE_SIZE;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'assignedTo', 'leadSourceId', 'leadRatingId', 'leadType'], true)) {
            $this->loadedCounts = [];
        }
    }

    public function moveLead(int $leadId, string $status, ?string $lostReason = null, ?int $position = null): void
    {
        $lead = Lead::query()->find($leadId);

        if ($lead === null) {
            Notification::make()
                ->danger()
                ->title(__('resources/lead.board.notifications.not_found'))
                ->send();

            return;
        }

        Gate::authorize('update', $lead);

        $originalStatus = $lead->status;

        $target = LeadStatus::tryFrom($status);

        if ($target === null) {
            Notification::make()
                ->danger()
                ->title(__('resources/lead.board.notifications.invalid_status'))
                ->send();

            return;
        }

        try {
            DB::transaction(function () use ($lead, $target, $lostReason, $position): void {
                app(MoveLeadStatus::class)->handle($lead, $target, $lostReason);

                $this->placeLead($lead, $target, $position);
            });
        } catch (ValidationException $exception) {
            Notification::make()
                ->danger()
                ->title(collect($exception->errors())->flatten()->first() ?? __('resources/lead.board.notifications.invalid_status'))
                ->send();

            return;
        } catch (InvalidArgumentException $exception) {
            Notification::make()
                ->danger()
                ->title($exception->getMessage())
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title($originalStatus === $target
                ? __('resources/lead.board.notifications.reordered', [
                    'lead' => $lead->display_name,
                ])
                : __('resources/lead.board.notifications.moved', [
                    'lead' => $lead->display_name,
                    'status' => $target->getLabel(),
                ]))
            ->send();
    }

    /**
     * @return array<int, string>
     */
    public function getUserOptions(): array
    {
        $currentUserId = auth()->id();

        return User::query()
            ->with('staff')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (User $user) use ($currentUserId): array {
                $label = $user->getKey() === $currentUserId
                    ? __('resources/lead.board.you_option', ['name' => $user->name])
                    : $user->name;

                return [$user->getKey() => UserAvatar::optionLabel($user, $label)];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function getLeadSourceOptions(): array
    {
        return LeadSource::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    /**
     * @return list<LeadStatus>
     */
    public function getMovableStatuses(): array
    {
        return array_values(array_filter(
            LeadStatus::cases(),
            fn (LeadStatus $status): bool => $status !== LeadStatus::Converted,
        ));
    }

    public function statusColor(LeadStatus $status): string
    {
        return match ($status->getColor()) {
            'info' => '#0ea5e9',
            'primary' => '#059669',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            default => '#9ca3af',
        };
    }

    /**
     * Simpan urutan kartu pada kolom tujuan berdasarkan posisi drop.
     */
    protected function placeLead(Lead $lead, LeadStatus $status, ?int $position): void
    {
        $leadIds = Lead::query()
            ->where('status', $status->value)
            ->whereKeyNot($lead->getKey())
            ->orderBy('board_position')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->all();

        $index = $position === null
            ? count($leadIds)
            : max(0, min($position, count($leadIds)));

        array_splice($leadIds, $index, 0, [$lead->getKey()]);

        foreach ($leadIds as $offset => $leadId) {
            Lead::query()->whereKey($leadId)->update([
                'board_position' => $offset + 1,
            ]);
        }
    }

    protected function query(): Builder
    {
        return Lead::query()
            ->when(filled($this->search), function (Builder $query): void {
                $search = '%'.$this->search.'%';

                $query->where(fn (Builder $query): Builder => $query
                    ->where('company_name', 'like', $search)
                    ->orWhere('contact_person', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('lead_number', 'like', $search));
            })
            ->when(filled($this->assignedTo), fn (Builder $query): Builder => $query->where(
                fn (Builder $query): Builder => $query
                    ->where('assigned_to', $this->assignedTo)
                    ->orWhereHas(
                        'salesTeam',
                        fn (Builder $query): Builder => $query->whereKey($this->assignedTo),
                    ),
            ))
            ->when(filled($this->leadSourceId), fn (Builder $query): Builder => $query->where('lead_source_id', $this->leadSourceId))
            ->when(filled($this->leadRatingId), fn (Builder $query): Builder => $query->where('lead_rating_id', $this->leadRatingId))
            ->when(filled($this->leadType), fn (Builder $query): Builder => $query->where('lead_type', $this->leadType));
    }
}
