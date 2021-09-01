<?php

namespace LidyaPos\Page\Providers;

use LidyaPos\Base\Supports\Helper;
use LidyaPos\Base\Traits\LoadAndPublishDataTrait;
use LidyaPos\Page\Models\Page;
use LidyaPos\Page\Repositories\Caches\PageCacheDecorator;
use LidyaPos\Page\Repositories\Eloquent\PageRepository;
use LidyaPos\Page\Repositories\Interfaces\PageInterface;
use LidyaPos\Shortcode\View\View;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * @since 02/07/2016 09:50 AM
 */
class PageServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->app->bind(PageInterface::class, function () {
            return new PageCacheDecorator(new PageRepository(new Page));
        });

        $this->setNamespace('packages/page')
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id'          => 'cms-core-page',
                'priority'    => 2,
                'parent_id'   => null,
                'name'        => 'packages/page::pages.menu_name',
                'icon'        => 'fa fa-book',
                'url'         => route('pages.index'),
                'permissions' => ['pages.index'],
            ]);

            if (function_exists('admin_bar')) {
                admin_bar()->registerLink(trans('packages/page::pages.menu_name'), route('pages.create'), 'add-new');
            }
        });

        if (function_exists('shortcode')) {
            view()->composer(['packages/page::themes.page'], function (View $view) {
                $view->withShortcodes();
            });
        }

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(EventServiceProvider::class);

        $this->app->register(RouteServiceProvider::class);
    }
}
