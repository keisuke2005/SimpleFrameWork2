<?php

namespace Backend\Foundation\Bases;

/**
* DB Access Object
* 
* PDOインスタンスを管理する
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package @package Backend\Foundation\Bases
*/
final class Dao
{
	/**
    * PDOオブジェクト
    * @access private
    * @var \PDO
    */
	private \PDO $pdo;

	/**
    * Daoオブジェクト群
    * @access private
    * @var array Dao[]
    */
	private static array $daos = array();

    /**
    * コンストラクタ
    *
    * mysqlかpostgresかを選べる
	* PDOを生成し、腹持ちにする
    * @access private
    * @param string DB種別
	* @param string DBホスト
	* @param string DBポート
	* @param string DB名
	* @param string DBユーザ
	* @param string DBパスワード
    */
	private function __construct(string $dbType,string $dbHost,string $dbPort,string $dbName,string $dbUser,string $dbPswd)
	{
        $dsn = match($dbType)
        {
            'pgsql' => sprintf(
                '%s:dbname=%s host=%s port=%s',
                $dbType,
                $dbName,
                $dbHost,
                $dbPort
            ),
            'mysql' => sprintf(
                '%s:host=%s:%s;dbname=%s;charset=utf8mb4',
                $dbType,
                $dbHost,
                $dbPort,
                $dbName
            )
        };
		$this->pdo = new \PDO($dsn,$dbUser,$dbPswd);
	}

    /**
    * インスタンス生成
    *
    * シングルトンでstatic変数に保管
    * @access public
	* @param string 保管キー
    * @param string DB種別
	* @param string DBホスト
	* @param string DBポート
	* @param string DB名
	* @param string DBユーザ
	* @param string DBパスワード
	* @return bool
    */
	public static function create(string $key,string $dbType,string $dbHost,string $dbPort,string $dbName,string $dbUser,string $dbPswd):bool
	{
		if(array_key_exists($key,self::$daos)) return false;
		try
		{
			$dao = new Dao($dbType,$dbHost,$dbPort,$dbName,$dbUser,$dbPswd);
			self::$daos[$key] = $dao;
			return true;
		}
		catch(PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
    * sqlファイル読み込み
    *
    * @access public
	* @static
	* @param string ファイルパス
	* @return string SQL文
    */
	public static function read(string $file):string
	{
		if(!file_exists($file)) return "";
		return file_get_contents($file);
	}

	/**
    * PDO取り出し
    *
    * @access public
	* @return \PDO
    */
	public function getPdo():\PDO
	{
		return $this->pdo;
	}

	/**
	* Dao取得
	* 
    * 保管されたDaoをキーで取得
    * @access public
	* @static
	* @param string キー
	* @return ?Dao
    */
    public static function get(string $key):?Dao
	{
		if(!array_key_exists($key,self::$daos)) return null;
        return self::$daos[$key];
    }

	/**
	* prepare
    * @access private
	* @param string SQL
    */
	private function prepare(string $sql)
	{
		try
		{
			return $this->pdo->prepare($sql);
		}
		catch(PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
	* bind
    * @access private
	* @param $stmt
	* @param array バインドパラメータ
    */
	private function bind($stmt,?array $params)
	{
		if(isset($params))
		{
			for($i = 0; $i < count($params); $i++)
			{
				$stmt->bindParam($i+1,$params[$i]);
			}
		}
		return $stmt;
	}

	/**
	* countRow
	*
	* 件数カウント
    * @access public
	* @param string SQL
	* @param ?array バインドパラメータ
	* @return int 件数
    */
	public function countRow(string $sql,?array $params = null): int
	{
		try
		{
			$stmt = $this->prepare($sql);
			$stmt = $this->bind($stmt,$params);
			$stmt->execute();
			return $stmt->rowCount();
		}
		catch(\PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
	* selectOneRow
	*
	* 一件引き
    * @access public
	* @param string SQL
	* @param ?array バインドパラメータ
	* @param array 結果
    */
	public function selectOneRow(string $sql,?array $params = null):array
	{
		try
		{
			$stmt = $this->prepare($sql);
			$stmt = $this->bind($stmt,$params);
			$stmt->execute();
			if($stmt->rowCount() < 1)
			{
				return array("result" => false,"data" => null);
			}
			$result =  $stmt->fetchAll(\PDO::FETCH_OBJ);
			return array(
				"result" => true,
				"data" => $result[0]
			);
		}
		catch(\PDOException $e)
		{
			return array("result" => false,'message' => $e->getMessage());
		}
	}

	/**
	* selectAnyRows
	*
	* 複数件取得
    * @access public
	* @param string SQL
	* @param ?array バインドパラメータ
	* @param array 結果
    */
	public function selectAnyRows(string $sql,?array $params = null): array
	{
		try
		{
			$stmt = $this->prepare($sql);
			$stmt = $this->bind($stmt,$params);
			$stmt->execute();
			if($stmt->rowCount() < 1)
			{
				return array("result" => false);
			}
			$result =  $stmt->fetchAll(\PDO::FETCH_OBJ);
			return array(
				"result" => true,
				"data" => $result
			);
		}
		catch(\PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
	* insert
	*
	* インサートID取得
    * @access public
	* @param string SQL
	* @param ?array バインドパラメータ
	* @param array 結果
    */
	public function insert(string $sql,?array $params = null): array
	{
		try
		{
			$stmt = $this->prepare($sql);
			$stmt = $this->bind($stmt,$params);
			$bool = $stmt->execute();
			$result = array(
				"result" => $bool,
				"id" => null
			);
			if($result)
			{
				$result["id"] = $this->pdo->lastInsertId();
			}
			return $result;
		}
		catch (\PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
	* modify
	*
	* 更新系SQL
    * @access public
	* @param string SQL
	* @param ?array バインドパラメータ
	* @param array 結果
    */
	public function modify(string $sql,?array $params = null): array
	{
		try
		{
			$stmt = $this->prepare($sql);
			$stmt = $this->bind($stmt,$params);
			$bool = $stmt->execute();
			return array(
				"result" => $bool,
				"mod_row" => $stmt->rowCount()
			);
		}
		catch (\PDOException $e)
		{
			die('DataBase Error:' .$e->getMessage());
		}
	}

	/**
	* begin
    * @access public
	* @return void
    */
	public function begin():void
	{
		$this->pdo->beginTransaction();
	}

	/**
	* commit
    * @access public
	* @return void
    */
	public function commit():void
	{
		$this->pdo->commit();
	}

	/**
	* rollback
    * @access public
	* @return void
    */
	public function rollback():void
	{
		$this->pdo->rollBack();
	}
}
