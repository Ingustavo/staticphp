<?php

/*
|--------------------------------------------------------------------------
| Apc session class
|
| Extends sessions class as an optional backup
|--------------------------------------------------------------------------
*/

namespace models;

class sessions_apc extends sessions
{
  private $db_link = FALSE;


  public function __construct(&$db_link = NULL)
  {
    $this->db_link = &$db_link;
    parent::__construct($this->db_link);
  }


  public function read($id)
  {
    $data = apc_fetch($this->prefix . $id);
    if (!empty($data))
    {
      return $data;
    }

    return (!empty($this->db_link) ? parent::read($id) : NULL);
  }


  public function write($id, $data)
  {
    apc_store($this->prefix . $id, $data, $this->expire);

    if (!empty($this->db_link))
    {
      parent::write($id, $data);
    }

    return TRUE;
  }


  public function destroy($id)
  {
    apc_delete($this->prefix . $id);

    if (!empty($this->db_link))
    {
      parent::destroy($id);
    }

    return TRUE;
  }
}

?>