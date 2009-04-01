<?php
/*
    "StaticPHP Framework" - Little PHP Framework

---------------------------------------------------------------------------------
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
---------------------------------------------------------------------------------

    Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/



class fv
{

  public static $errors = null;
  public static $errors_all = null;

  public static $post = array();
  private static $rules = array();
  
  private static $default_errors = array(
    'missing' => 'Field "!name" is missing',
    'required' => 'Field "!name" is required',
    'email' => '"!value" is not a correct e-mail address',
    'date' => '"!value" is not a correct date format',
    'ipv4' => '"!value" is not a correct ipv4 address',
    'ipv6' => '"!value" is not a correct ipv6 address',
    'credit_card' => '"!value" is not a correct credit card number',
    
    'length' => 'Field "!name" has not correct length',
    'equal' => 'Field "!name" has wrong value',
    'format' => 'Field "!name" has not a correct format',
    
    'integer' => 'Field "!name" must be integer',
    'float' => 'Field "!name" must be float number',
    'string' => 'Field "!name" can contain only letters, []$/!.?()-\'" and space chars',
    
    'upload_required' => 'Field "!name" is required',
    'upload_size' => 'Uploaded file is to large',
    'upload_ext' => 'File type is not allowed',
  );




  public static function init()
  {
    foreach (func_get_args() as $item)
    {
      if (is_array($item))
      {
        self::$post = array_merge(self::$post, $item);
      }
    }
  }
  
  
  public static function errors($errors)
  {
    self::$default_errors = array_merge(self::$default_errors, $errors);
  }


  public static function add_rules($rules)
  {
    self::$rules = array_merge(self::$rules, $rules);
  }


  public static function validate()
  {
    foreach (self::$rules as $name => $value)
    {
      if (!isset(self::$post[$name]))
      {
        self::set_error('missing', $name);
      }
      else
      {
        self::filter_field($name);
        self::validate_field($name);
      }
    }

    return empty(self::$errors);
  }


  public static function filter_field($name)
  {
    if (!empty(self::$rules[$name]['filter']))
    {
      foreach (self::$rules[$name]['filter'] as $item)
      {
        $matches = $args = array();
        if (preg_match('/(\w+)\[(.*)\]/', $item, $matches))
        {
          $item = $matches[1];
          $args = explode(',', $matches[2]);
        }

        if (function_exists($item))
        {
          array_unshift($args, self::$post[$name]);
          self::$post[$name] = call_user_func_array($item, $args);
        }
      }
    }
  }


  public static function validate_field($name)
  {
    if (!empty(self::$rules[$name]['valid']))
    {
      foreach (self::$rules[$name]['valid'] as $item)
      {
        $matches = $args = array();
        if (preg_match('/(\w+)\[(.*)\]/', $item, $matches))
        {
          $item = $matches[1];
          $args = explode(',', $matches[2]);
          $args = str_replace('&#44;', ',', $args);
        }

        if (in_array($item, array_keys(self::$default_errors)))
        {
          if (method_exists('fv', 'valid_'.$item))
          {
            array_unshift($args, self::$post[$name]);
            if (call_user_func_array(array('fv', 'valid_'.$item), $args) === false)
            {
              self::set_error($item, $name, self::$post[$name]);
            }
          }
        }
      }
    }
  }


  public static function set_error($type, $name, $value = '')
  {
    self::$errors_all[] = &$tmp;
    self::$errors[$name][] = &$tmp;

    $tmp = strtr(
      (!empty(self::$rules[$name]['errors'][$type]) ? self::$rules[$name]['errors'][$type] : self::$default_errors[$type]), 
      array('!name' => $name, '!value' => $value)
    );
  }

  public static function get_error($name)
  {
    return (empty(self::$errors[$name]) ? false : self::$errors[$name]);
  }
  
  
  
  // --- VALIDATION METHODS ---
  
  public static function valid_required($value)
  {
    return !empty($value);
  }

  public static function valid_email($email)
  {
    return (bool) preg_match("/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/ix", $email);
  }

  public static function valid_date($value, $format = '^(19|20)[0-9]{2}[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$')
  {
    return self::valid_format($value, $format);
  }
  
  public static function valid_ipv4($value)
  {
    return (bool) preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $value);
  }
  
  public static function valid_ipv6($value)
  {
    return (bool) preg_match('/^(^(([0-9A-F]{1,4}(((:[0-9A-F]{1,4}){5}::[0-9A-F]{1,4})|((:[0-9A-F]{1,4}){4}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,1})|((:[0-9A-F]{1,4}){3}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,2})|((:[0-9A-F]{1,4}){2}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,3})|(:[0-9A-F]{1,4}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,4})|(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,5})|(:[0-9A-F]{1,4}){7}))$|^(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,6})$)|^::$)|^((([0-9A-F]{1,4}(((:[0-9A-F]{1,4}){3}::([0-9A-F]{1,4}){1})|((:[0-9A-F]{1,4}){2}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,1})|((:[0-9A-F]{1,4}){1}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,2})|(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,3})|((:[0-9A-F]{1,4}){0,5})))|([:]{2}[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,4})):|::)((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{0,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{0,2})$$/', $value);
  }
  
  public static function valid_credit_card($value)
  {
    $value = preg_replace('/[^0-9]+/', '', $value);
    return (bool) preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/', $value);
  }
  
  public static function valid_length($value, $from, $to = null)
  {
    $len = strlen($value);

    switch (true)
    {
      case ($to == '>'):
        return ($len >= $from);
      break;
      
      case ($to == '>'):
        return ($len <= $from);
      break;
      
      case (ctype_digit($to)):
        return ($len >= $from && $len <= $to);
      break;

      case ($to == '='):
      default:
        return ($len == $from);
      break;
    }
  }
  
  public static function valid_equal($value, $equal, $cast = false)
  {
    return ($cast == false ? $value === $equal : $value == $equal);
  }

  public static function valid_format($value, $format = '')
  {
    $format = str_replace('/', '\\/', $format);
    return (bool) preg_match("/$format/", $value);
  }
  
  public static function valid_integer($value)
  {
    return (bool) preg_match('/^\d+$/x', $value);
  }
  
  public static function valid_float($value, $delimiter = '.')
  {
    return (bool) preg_match('/^\d+'.preg_quote($delimiter, '/').'?\d+$/', $value);
  }
  
  public static function valid_string($value)
  {
    return (bool) preg_match('/^[a-z]+$/i', $value);
  }




  public static function valid_upload_required($upload)
  {
    return (is_array($upload) && !empty($upload['name']) && !empty($upload['tmp_name']) && !empty($upload['size']));
  }
  
  public static function valid_upload_size($upload, $size)
  {
    if (self::valid_upload_required($upload))
    {
      return ($upload['size'] <= $size);
    }
  }
  
  public static function valid_upload_ext($upload, $extensions)
  {
    if (self::valid_upload_required($upload))
    {
      $ext = explode(' ', $extensions);
      $tmp = explode('.', $upload['name']);

      return in_array(end($tmp), $ext);
    }
  }
  
  
  
  // --- FORM HELPERS ----

  public static function isget()
  {
    return (strtolower($_SERVER['REQUEST_METHOD']) === 'get');
  }
  
  public static function ispost($isset = null)
  {
    // Check if post
    if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post')
    {
      return false;
    }

    // Check if isset keys in POST data
    if ($isset !== null)
    {
      foreach((array) $isset as $key)
      {
        if (!isset(self::$post[$key]))
        {
          return false;
        }
      }
    }
    return true;
  }
  
  
  
  public static function set_input($name)
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo ' value="'.(!empty($field) ? $field : '').'"';
  }

  public static function set_select($name, $test = '')
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo ((is_array($field) && in_array($test, $field)) || $field == $test ? ' selected="selected"' : '');
  }


	public static function set_checkbox($name)
	{
		if (($field = self::get_field($name)) == false)
		{
			return false;
		}
		echo (!empty($field) ? ' checked="checked"' : '');
	}


  public static function set_value($name)
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo $field;
  }
  
  
  private static function get_field($name)
  {
    $field = self::$post;

    foreach ((array)$name as $item)
    {
      if (isset($field[$item]))
      {
        $field =& $field[$item];
      }
      else
      {
        return false;
      }
    }
    return $field;
  }
  
}

?>