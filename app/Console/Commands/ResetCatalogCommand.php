<?php

namespace App\Console\Commands;

use App\Catalog\CatalogData;
use App\Catalog\ProductRepository;
use Illuminate\Console\Command;

class ResetCatalogCommand extends Command
{
    protected $signature = 'catalog:reset {--force : Skip the confirmation prompt}';

    protected $description = 'Discard local changes and restore the 25 products from the source document';

    public function handle(ProductRepository $products): int
    {
        if (! $products->hasLocalChanges() && ! $this->option('force')) {
            $this->components->info('Catalog already matches the source document ('.CatalogData::total().' products).');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Discard local changes and restore the document catalog?', true)) {
            $this->components->warn('Nothing was changed.');

            return self::SUCCESS;
        }

        $products->reset();

        $this->components->info('Catalog restored to the original '.CatalogData::total().' products.');

        return self::SUCCESS;
    }
}
