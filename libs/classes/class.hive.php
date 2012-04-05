<?php
class Hive
{
	public $mTransport = '';
	public $mProtocol = '';
	public $mClient = '';
	
	public function __construct()
	{
		$this->mTransport = new TSocket(HOST, PORT);echo HOST;echo PORT;
		$this->mProtocol = new TBinaryProtocol($this->mTransport);
		$this->mClient = new ThriftHiveClient($this->mProtocol);
	}
	
	public function Execute($pSql)
	{
		$this->mTransport->open();
		$this->mClient->execute($sql);
	}
	
	public function Fetch($pSql)
	{
		$array = $this->Query($pSql);
		return $array;
	}

	public function __destruct()
	{
		$this->mTransport->close();
	}
}

class MysqlMeta extends Hive
{
	private $mDb;
	private $mRes;
	
	public function __construct()
	{
		$this->mDb = mysql_connect(METADB.":".METAPORT,METAUSER,METAPASS);
		mysql_select_db(METANAME,$this->mDb);
	}
	
	private function Query($pSql)
	{
		$this->mRes = mysql_query($pSql,$this->mDb);
	}
	
	public function GetResultRow($pSql)
	{
		$this->Query($pSql);
		$i = 0;
		while($array = mysql_fetch_row($this->mRes))
		{
			foreach($array as $k => $v)
			{
				$arr[$i][$k] = $v;
			}
			$i++;
		}
		if($arr[0][0] != "")
		{
			return $arr;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function GetResultKey($pSql)
	{
		$this->Query($pSql);
		$i = 0;
		while($array = mysql_fetch_array($this->mRes))
		{
			foreach($array as $k => $v)
			{
				$arr[$i][$k] = $v;
			}
			$i++;
		}
		return $arr;
	}
	
	public function Close()
	{
		$this->__destruct();
	}
	
	public function __destruct()
	{
		mysql_close($this->mDb);
	}
}
?>