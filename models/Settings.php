<?php namespace Octobro\MediumBlog\Models;

use Model;
use Validator;

class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'octobro_mediumblog_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public $rules = [
        'sites'     => 'medium:.com,.medium,.medium.',
    ];

    public function __construct()
    {
        parent::__construct();
        
        $this->validateExtended();
        
    }

    public function validateExtended()
    {
        Validator::extend('medium', 'Octobro\MediumBlog\Rules\MediumRule', 'Please check your :attribute must contain medium domain.');
    }
}
