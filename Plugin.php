<?php namespace Octobro\MediumBlog;

use Backend;
use Event;
use Octobro\MediumBlog\Models\Settings as MediumSettings;
use RainLab\Blog\Controllers\Posts as RainLabPostsController;
use RainLab\Blog\Models\Post;
use System\Classes\PluginBase;

/**
 * mediumBlog Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['RainLab.Blog'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'mediumBlog',
            'description' => 'No description provided yet...',
            'author'      => 'octobro',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Post::extend(function($model) {
            $model->addDynamicMethod('getMediumImageAttribute', function() use ($model) {
                if($model->source_by == 'medium') {
                    $texthtml = $model->content_html;
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $texthtml, $image);
                    $model->attributes['medium_image'] = $image[1]; 
                    return $model->attributes['medium_image'];
                }
                return;
            });
        });

        RainLabPostsController::extendListFilterScopes(function($filter) {
            $filter->addScopes([
                'medium_blog' => [
                    'label'      => 'Medium Post',
                    'type'       => 'checkbox',
                    'conditions' => "source_by = 'medium'"
                ]
            ]);
        });
    }


    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Medium Blog',
                'description' => 'Manage available your medium feed.',
                'category'    => 'Blog',
                'icon'        => 'icon-medium',
                'class'       => 'Octobro\MediumBlog\Models\Settings',
                'order'       => 500,
                'keywords'    => 'Medium Blog'
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            \Queue::push('Octobro\MediumBlog\Jobs\FetchPosts');
        })->everyMinute();
    }
}
