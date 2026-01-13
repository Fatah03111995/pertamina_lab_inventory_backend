<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Closure;
use BackedEnum;

class GenericModelExports implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithChunkReading
{
    use Exportable;

    protected Builder $query;
    protected Table $table;
    protected array $state;
    protected $filter; // null | closure | obj with apply(Builder, array): Builder

    /**
     * @param Builder $query => hasil filter filament table
     * @param array $columns => kolom yang akan di export
     */

    public function __construct(Builder $query, Table $table, array $state = [], $filter = null)
    {
        $this->query = $query;
        $this->table = $table;
        $this->state = $state;
        $this->filter = $filter;
    }

    public function query()
    {
        $q = clone $this->query; // agar tidak mempengaruhi query asli

        if ($this->filter instanceof Closure) {
            return ($this->filter)($q, $this->state);
        }

        if (is_object($this->filter) && method_exists($this->filter, 'apply')) {
            return $this->filter->apply($q, $this->state);
        }

        return $q;
    }

    public function headings(): array
    {
        $colActives = $this->table->getVisibleColumns();
        return array_map(function ($col) {
            $label = $col->getLabel();
            $label = str_replace('_', ' ', $label);
            $label = strtoupper($label);
            return $label;
        }, $colActives);
    }

    public function map($row): array
    {
        $out = [];
        $colActives = $this->table->getVisibleColumns();
        foreach ($colActives as $col) {
            // Ambil nilai dari properti atau relasi
            $value = data_get($row, $col->getName());

            if ($value instanceof BackedEnum) {
                $value = $value->value;
            }
            if ($value instanceof \Carbon\Carbon) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_bool($value)) {
                $value = $value ? 'Ya' : 'Tidak';
            }

            $out[] = $value;
        }

        return $out;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

}
