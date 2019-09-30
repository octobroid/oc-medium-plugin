<?php namespace Octobro\MediumBlog\Rules;

use Illuminate\Contracts\Validation\Rule;

class MediumRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
    }

    /**
     * Validation callback method.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    public function validate($attribute, $value, $params)
    {
        $result = [];
        $values = explode(',', $value);
        if(strlen($value) > 0){
            foreach($values as $site){
                foreach($params as $parameter){
                    if(strpos($site, $parameter) == false){
                        $result[] = false;
                    }
                }
            }    
            return in_array(false, $result) ? false : true;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please check your :attribute must contain medium domain.';
    }
}