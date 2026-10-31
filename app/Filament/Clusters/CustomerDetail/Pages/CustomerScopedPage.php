<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use Filament\Tables\Columns\Column;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

abstract class CustomerScopedPage extends CustomerDetailPage implements HasTable
{
    use InteractsWithTable;

    /**
     * Nama relasi pada model Customer.
     */
    abstract protected function relationship(): string;

    /**
     * @return array<Column>
     */
    abstract protected function columns(): array;

    abstract protected function defaultSort(): string;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->customerRecord->{$this->relationship()}()->getQuery())
            ->columns($this->columns())
            ->recordUrl(fn (Model $record): ?string => $this->recordUrl($record))
            ->defaultSort($this->defaultSort(), 'desc');
    }

    protected function recordUrl(Model $record): ?string
    {
        return null;
    }
}
