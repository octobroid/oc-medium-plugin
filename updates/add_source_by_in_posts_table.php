<?php namespace Octobro\MediumBlog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddSourceByInPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'source_by')) {
            return;
        }

        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('source_by')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'source_by')) {
            Schema::table('rainlab_blog_posts', function ($table) {
                $table->dropColumn('source_by');
            });
        }
    }
}
