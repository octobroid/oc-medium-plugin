<?php namespace Octobro\MediumBlog\Jobs;

use Carbon\Carbon;
use Octobro\MediumBlog\Helpers\XmlGenerator;
use League\HTMLToMarkdown\HtmlConverter;
use Octobro\MediumBlog\Models\Post;

class FetchPosts
{
    public function fire($job, $data)
    {
        Post::whereSourceBy('medium')->delete();

        $link = array_get($data,'link');

        $xmlMediumFeed = simplexml_load_file($link);
        $mediumPosts = data_get($this->xmlGenerator()->xmlToArray($xmlMediumFeed), 'rss.channel.item');

        if(!$xmlMediumFeed || sizeof($mediumPosts) < 0){
            $job->delete();
            return;
        }


        collect($mediumPosts)->each(function($post){
            $this->createPost($post);
        });

        $job->delete();
        return;

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
        $post = new Post;
        $post->source_by    = 'medium';
        $post->creator      = $data['dc:creator'];
        $post->published_at = Carbon::parse($data['pubDate']);
        $post->published    = true;
        $post->content      = $this->markdownGenerator($data['content:encoded']);
        $post->content_html = $data['content:encoded'];
        $post->title        = $data['title'];
        $post->slug         = str_slug($data['title']);

        $post->save();
    }
}
