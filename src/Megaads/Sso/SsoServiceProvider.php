<?php 
namespace Megaads\Sso;

use Illuminate\Support\ServiceProvider;

class SsoServiceProvider extends ServiceProvider 
{
    public function boot() {
        $this->package('megaads/sso');
        include __DIR__ . '/../../customFilters.php';
    }

    public function register() {

    }
}