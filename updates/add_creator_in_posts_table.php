<?php namespace Octobro\MediumBlog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddCreatorInPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'octobro_medium_creator')) {
            return;
        }

        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('octobro_medium_creator')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'octobro_medium_creator')) {
            Schema::table('rainlab_blog_posts', function ($table) {
                $table->dropColumn('octobro_medium_creator');
            });
        }
    }
}
