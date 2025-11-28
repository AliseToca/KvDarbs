<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\ExportAction;

class ResourceExportAction extends ExportAction
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Export')
            ->icon('heroicon-c-arrow-down-tray');
    }

    public function setDefaultExporter(): static
    {
        $this->exporter('App\\Filament\\Exports\\' . class_basename($this->getModel()) . 'Exporter');

        return $this;
    }
}
