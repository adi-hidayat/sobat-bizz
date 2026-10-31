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
    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.board-leads';

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
            Action::make('create')
                ->label(__('resources/lead.actions.create'))
                ->icon('heroicon-o-plus')
                ->url(LeadResource::getUrl('create')),
        ];
    }

    public function mount(): void
    {
        session(['leads.view_mode' => 'board']);
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
     *     columns: list<array{status: LeadStatus, leads: Collection<int, Lead>, count: int, value: float}>
     * }
     */
    public function getBoard(): array
    {
        $statuses = [
            LeadStatus::New,
            LeadStatus::Contacted,
            LeadStatus::Qualified,
            LeadStatus::Unqualified,
            LeadStatus::Lost,
            LeadStatus::Converted,
        ];

        $leads = $this->query()
            ->with(['leadSource', 'assignedSalesPerson', 'rating'])
            ->orderBy('board_position')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (Lead $lead): string => $lead->status->value);

        $columns = array_map(function (LeadStatus $status) use ($leads): array {
            $items = $leads->get($status->value, collect());

            return [
                'status' => $status,
                'leads' => $items,
                'count' => $items->count(),
                'value' => (float) $items->sum(fn (Lead $lead): float => (float) $lead->estimated_value),
            ];
        }, $statuses);

        return [
            'columns' => $columns,
        ];
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

    public function ratingColor(?LeadRating $rating): string
    {
        return match ($rating?->color) {
            'primary' => '#059669',
            'info' => '#0ea5e9',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'gray' => '#9ca3af',
            default => $rating?->color ?: '#9ca3af',
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
            ->when(filled($this->assignedTo), fn (Builder $query): Builder => $query->where('assigned_to', $this->assignedTo))
            ->when(filled($this->leadSourceId), fn (Builder $query): Builder => $query->where('lead_source_id', $this->leadSourceId))
            ->when(filled($this->leadRatingId), fn (Builder $query): Builder => $query->where('lead_rating_id', $this->leadRatingId))
            ->when(filled($this->leadType), fn (Builder $query): Builder => $query->where('lead_type', $this->leadType));
    }
}
