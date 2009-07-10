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


class languages
{

    private static $db_link = null;
    
    
    public static function &db_link()
    {
        if (!empty(self::$db_link))
        {
            return self::$db_link;
        }
    }


    public static function init()
    {
        // Open new connection to DB
        self::$db_link = new PDO('sqlite:'. (is_file(g('config')->language_path) ? g('config')->language_path : APP_PATH . g('config')->language_path));
    }


    public static function query($query, $data = null)
    {
        if (!empty($query))
        {
            if (is_null(self::$db_link))
            {
                throw new Exception('No connection to database');
            }
            else
            {
                $prepare = self::$db_link->prepare($query);
                $errorCode = self::$db_link->errorCode();

                // Check if errorCode = empty
                if ($errorCode == '00000')
                {
                    $prepare->setFetchMode(PDO::FETCH_OBJ);
                    $prepare->execute($data);
                    return $prepare;
                }
                else
                {
                    $errorInfo = self::$db_link->errorInfo();
                    throw new Exception($errorInfo[2]);
                }
            }
        }
    }
    
    
    public static function exec($query, $data)
    {
        if (!empty($query))
        {
            if (is_null(self::$db_link))
            {
                throw new Exception('No connection to database');
            }
            else
            {
                // Create null return value
                $prepare = null;

                // Try execute query
                try
                {
                    self::$db_link->beginTransaction();
                        $prepare = self::query($query, $data);
                    self::$db_link->commit();
                }
                catch(PDOException $e)
                {
                    self::$db_link->rollback();
                    throw new Exception($e->getMessage());
                }
                
                return $prepare;
            }
        }
    }
}

?>