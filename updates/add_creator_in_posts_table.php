<?php namespace Octobro\MediumBlog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddCreatorInPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'creator')) {
            return;
        }

        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('creator')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'creator')) {
            Schema::table('rainlab_blog_posts', function ($table) {
                $table->dropColumn('creator');
            });
        }
    }
}
