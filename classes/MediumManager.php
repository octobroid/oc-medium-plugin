<?php namespace Octobro\MediumBlog\Classes;

use October\Rain\Support\Collection;
use Octobro\MediumBlog\Models\Settings as MediumSettings;

use Carbon\Carbon;
use Octobro\MediumBlog\Helpers\XmlGenerator;
use League\HTMLToMarkdown\HtmlConverter;
use Octobro\MediumBlog\Models\Post;

class MediumManager
{
    use \October\Rain\Support\Traits\Singleton;

    protected function init()
    {
    }

    public function collectMedium()
    {
        $medium_links = $this->listMediumLinks();

        foreach ($medium_links as $medium_link) {
            $this->fetch($medium_link);
            continue;
        }
    }

    protected function fetch($link){

        $xmlMediumFeed = simplexml_load_file($link);
        $mediumPosts = data_get($this->xmlGenerator()->xmlToArray($xmlMediumFeed), 'rss.channel.item');

        if(!$xmlMediumFeed || sizeof($mediumPosts) < 0){
            return;
        }

        if(sizeof($mediumPosts) == $this->getPost()->count()){
            return;
        }

        collect($mediumPosts)->each(function($post){
            $this->createPost($post);
        });

    }

    public function listMediumLinks()
    {
        $accounts = $this->fetchAccountsLink();
        $sites = $this->fetchSitesLink();

        return array_merge($accounts, $sites);
    }

    protected function fetchAccountsLink()
    {
        $accounts = explode(',', MediumSettings::get('accounts'));

        return collect($accounts)->map(function($account) {
            $medium_link = 'https://medium.com/feed/'.$account;
            return $medium_link;
        })->all();
        
    }

    protected function fetchSitesLink()
    {
        $sites = explode(',', MediumSettings::get('sites'));

        return collect($sites)->map(function($site) {
            $medium_link = 'https://'. $site .'/feed';
            return $medium_link;
        })->all();
    }

    protected function getPost()
    {
        return Post::whereSourceBy('medium')->get();
    }

    protected function xmlGenerator()
    {
        $generator = new XmlGenerator;
        return $generator;
    }

    protected function markdownGenerator($html)
    {
        $converter = new HtmlConverter();
        return $converter->convert($html);
    }

    public function createPost($data)
    {
        if(!in_array(str_slug($data['title']), $this->getPost()->pluck('slug')->toArray())){
            if(isset($data['content:encoded'])){
                $post = new Post;
                $post->octobro_medium_source_by = 'medium';
                $post->octobro_medium_creator   = $data['dc:creator'];
                $post->published_at             = Carbon::parse($data['pubDate']);
                $post->published                = true;
                $post->content                  = $this->markdownGenerator($data['content:encoded']);
                $post->content_html             = $data['content:encoded'];
                $post->title                    = $data['title'];
                $post->slug                     = str_slug($data['title']);

                $post->save();
            }
        }
    }
}