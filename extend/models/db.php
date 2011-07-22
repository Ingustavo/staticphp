<?php

namespace models;

class db
{
  public static $queries = NULL;
  public static $query_count = 0;

  private static $db_links;
  private static $last_statement;


  // -- INIT
  public static function init($name = 'default')
  {
    // Check if there is such configuration
    if (empty(\load::$config['db']['pdo'][$name]))
    {
      return FALSE;
    }

    // Set params
    $params = \load::$config['db']['pdo'][$name];

    // Don't make a new connection if there is one connected with the name
    if (!empty(self::$db_links[$name]))
    {
      return self::$db_links[$name];
    }

    // Set default connection
    if (empty(self::$db_links['default']))
    {
      self::$db_links['default'] = &self::$db_links[$name];
    }

    // Open new connection to DB
    self::$db_links[$name] = new \PDO($params['string'], $params['username'], $params['password'], array(
      \PDO::ATTR_ERRMODE => (empty(\load::$config['debug']) ? \PDO::ERRMODE_SILENT : \PDO::ERRMODE_EXCEPTION),
      \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
      \PDO::ATTR_PERSISTENT => $params['persistent']
    ));

    // Set encoding - for mysql only
    if (!empty($params['charset']) && self::$db_links[$name]->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'mysql')
    {
      self::$db_links[$name]->exec('SET NAMES '. $params['charset'] .';');
    }
  }



	// -- Query
  public static function query($query, $data = NULL, $name = 'default')
  {
    $db_link = &self::init($name);

    if (empty($query))
    {
      return NULL;
    }

    if (empty($db_link))
    {
      throw new \Exception('No connection to database');
    }

    // Do request
    self::$last_statement = $db_link->prepare($query);
    self::$last_statement->execute((array) $data);

    // Count Queries
    if (!empty(\load::$config['debug']))
    {
      ++self::$query_count;
      self::$queries[$name][] = self::$last_statement->queryString;
    }

    // Return last statement
    return self::$last_statement;
  }



	// -- Fetch wrapper
  public static function fetch($query, $data = array(), $name = 'default')
  {
    return self::query($query, $data, $name)->fetch();
  }



	// -- FetchAll wrapper
  public static function fetchAll($query, $data = array(), $name = 'default')
  {
    return self::query($query, $data, $name)->fetchAll();
  }



	// -- Make update string from and array. Add "!" at start of the key to avoid escaping.
  public static function update($table, $data, $where, $name = 'default')
  {
    // Make SET
    foreach ((array)$data as $key => $value)
    {
      if ($key[0] == '!')
      {
        $set[] = \load::$config['db']['pdo'][$name]['wrap_column'] . substr($key, 1) . \load::$config['db']['pdo'][$name]['wrap_column'] ." = {$value}";
      }
      else
      {
        $set[] = \load::$config['db']['pdo'][$name]['wrap_column'] . $key . \load::$config['db']['pdo'][$name]['wrap_column'] .' = ?';
        $params[] = $value;
      }
    }

    // Make WHERE
    foreach ((array)$where as $key => $value)
    {
			$c = '=';
			$expl = explode(' ', $key);
			if (count($expl) > 1)
			{
				$key = $expl[0];
				$c = $expl[1];
			}

      if ($key[0] == '!')
      {
        $cond[] = \load::$config['db']['pdo'][$name]['wrap_column'] . substr($key, 1) . \load::$config['db']['pdo'][$name]['wrap_column'] . " {$c} {$value}";
      }
      else
      {
        $cond[] = \load::$config['db']['pdo'][$name]['wrap_column'] . $key . \load::$config['db']['pdo'][$name]['wrap_column'] . " {$c} ?";
        $params[] = $value;
      }
    }

    // Compile SET and WHERE
    $set = implode(', ', $set);
    if (!empty($cond))
    {
      $cond = 'WHERE ' . implode(' AND ', $cond);
    }

    // Run Query
    return self::query("UPDATE {$table} SET {$set} {$cond};", $params, $name);
  }



	// -- Make insert string from and array. Add "!" at start of the key to avoid escaping
  public static function insert($table, $data, $name = 'default')
  {
    foreach ((array)$data as $key => $value)
    {
      if ($key[0] == '!')
      {
        $keys[] = \load::$config['db']['pdo'][$name]['wrap_column'] . substr($key, 1) . \load::$config['db']['pdo'][$name]['wrap_column'];
        $values[] = $value;
      }
      else
      {
        $keys[] = \load::$config['db']['pdo'][$name]['wrap_column'] . $key . \load::$config['db']['pdo'][$name]['wrap_column'];
        $values[] = '?';
        $params[] = $value;
      }
    }

    // Compile KEYS and VALUES
    $keys = implode(', ', $keys);
    $values = implode(', ', $values);

    // Run Query
    return self::query("INSERT INTO {$table} ({$keys}) VALUES ({$values})", $params, $name);
  }



	// -- Return link to the database connection for raw actions on it
  public static function &db_link($name = 'default')
  {
    return self::$db_links[$name];
  }



	// -- Return the last query statement
  public static function &last_statement()
  {
    if (!empty(self::$last_statement))
    {
      return self::$last_statement;
    }
  }



	// -- Return the last query executed
  public static function last_query()
  {
    return empty(self::$last_statement) ? NULL : self::$last_statement->queryString;
  }



	// -- Return the last insert id is created into database
  public static function last_insert_id($sequence_name = '', $sql = FALSE, $name = 'default')
  {
		if (empty($sql))
		{
			return self::$db_links[$name]->lastInsertId($sequence_name);
		}
		else
		{
			$res = self::query('SELECT LAST_INSERT_ID() as id');
			return (empty($res->id) ? NULL : $res->id);
		}
  }
}

?>