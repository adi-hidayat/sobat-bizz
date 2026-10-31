<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Proposal;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerMorphMap();
        $this->configureFilamentUi();
    }

    /**
     * Nama model tidak disimpan langsung di database (PRD 5.7).
     */
    private function registerMorphMap(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'lead' => Lead::class,
            'customer' => Customer::class,
            'supplier' => Supplier::class,
            'product' => Product::class,
            'proposal' => Proposal::class,
            'quotation' => Quotation::class,
            'incoming_po' => IncomingPo::class,
            'purchase_order' => PurchaseOrder::class,
            'delivery_order' => DeliveryOrder::class,
            'invoice' => Invoice::class,
            'payment' => Payment::class,
            'activity' => ActivityLog::class,
        ]);
    }

    /**
     * Konfigurasi tampilan Filament yang berlaku untuk seluruh panel:
     * semua kolom tabel dapat disembunyikan dan aksi lihat tampil sebagai
     * slide-over dengan tab.
     */
    private function configureFilamentUi(): void
    {
        TextColumn::configureUsing(
            fn (TextColumn $column): TextColumn => $column->toggleable(),
        );

        ViewAction::configureUsing(
            fn (ViewAction $action): ViewAction => $action
                ->label(__('common.actions.view'))
                ->modal()
                ->slideOver()
                ->modalWidth(Width::FiveExtraLarge),
        );

        EditAction::configureUsing(
            fn (EditAction $action): EditAction => $action->label(__('common.actions.edit')),
        );
    }
}
