<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		
		// blade extens money indonesia
		Blade::directive('thunder_mail_money_indo', function($expression)
		{
			return "<?php echo 'IDR '.number_format($expression, 0, ',', '.'); ?>";
		});

		// blade extens money indonesia for email
		Blade::directive('thunder_mail_money_indo_without_IDR', function($expression)
		{
			return "<?php echo number_format($expression, 0, ',', '.'); ?>";
		});
	}
}
