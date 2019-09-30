<?php namespace Octobro\MediumBlog\Jobs;

use Octobro\MediumBlog\Classes\MediumManager;

class FetchPosts
{
    public function fire($job, $data)
    {

        $medium = MediumManager::instance();
        $medium->collectMedium();

        $job->delete();
        return;

    }
}
