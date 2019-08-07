<?php namespace Octobro\MediumBlog\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'octobro_mediumblog_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
