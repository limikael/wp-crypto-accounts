<?php

	class ActiveRecord {

		private static $classes=array();
		private static $pdo;
		private static $tablePrefix;

		/**
		 * Set table to operate on.
		 */
		protected static final function setTable($name) {
			self::$classes[get_called_class()]["table"]=self::$tablePrefix.$name;
		}

		/**
		 * Get full table name.
		 */
		protected static final function getFullTableName() {
			self::init();

			return self::$classes[get_called_class()]["table"];
		}

		/**
		 * Add field.
		 */
		protected static final function addField($name, $definition) {
			if (!isset(self::$classes[get_called_class()]["primaryKey"]))
				self::$classes[get_called_class()]["primaryKey"]=$name;

			self::$classes[get_called_class()]["fields"][$name]=$definition;
		}

		/**
		 * Init.
		 */
		private final static function init() {
			if (!isset(self::$pdo))
				throw new Exception("PDO not set for ActiveRecord");

			$class=get_called_class();

			if (isset(self::$classes[$class]))
				return;

			self::$classes[$class]=array("fields"=>array());

			static::initialize();

			if (!isset(self::$classes[$class]["table"]))
				self::$classes[$class]["table"]=self::$tablePrefix.strtolower(get_called_class());
		}

		/**
		 * Set pdo.
		 */
		public static final function setPdo($pdo) {
			self::$pdo=$pdo;
		}

		/**
		 * Set table prefix.
		 */
		public static final function setTablePrefix($prefix) {
			self::$tablePrefix=$prefix;
		}

		/**
		 * Create underlying table.
		 */
		public final static function install() {
			self::init();

			$table=self::$classes[get_called_class()]["table"];
			$fields=self::$classes[get_called_class()]["fields"];
			$primaryKey=self::$classes[get_called_class()]["primaryKey"];

			// Create table if it doesn't exist.
			$qs="CREATE TABLE IF NOT EXISTS ".$table." (";

			foreach ($fields as $name=>$declaration)
				$qs.=$name." ".$declaration.", ";

			$qs.="primary key(".$primaryKey."))";

			$createStatement=self::$pdo->query($qs);
			if (!$createStatement)
				throw new Exception("Unable to create table: ".join(",",self::$pdo->errorInfo()));

			$createStatement->closeCursor();

			// Check current state of database.
			$describeStatement=self::$pdo->query("DESCRIBE ".$table);
			if (!$describeStatement)
				throw new Exception("Unable to set up table: ".join(",",self::$pdo->errorInfo()));

			$describeResult=$describeStatement->fetchAll();
			$describeStatement->closeCursor();

			$existing=array();
			foreach ($describeResult as $describeRow)
				$existing[]=$describeRow["Field"];

			// Create or modify existing fields.
			foreach ($fields as $name=>$declaration) {
				if (in_array($name,$existing)) {
					$q="ALTER TABLE `$table` MODIFY $name $declaration";
				}

				else {
					$q="ALTER TABLE `$table` ADD `$name` $declaration";
				}

				$updateStatement=self::$pdo->query($q);
				if (!$updateStatement)
					throw new Exception("Unable to update table: ".join(",",self::$pdo->errorInfo()));

				$updateStatement->closeCursor();
			}

			// Drup unused fields.
			$currentFieldNames=array_keys($fields);
			foreach ($existing as $existingField) {
				if (!in_array($existingField, $currentFieldNames)) {
					$updateStatement=self::$pdo->query("ALTER TABLE $table DROP $existingField");

					if (!$updateStatement)
						throw new Exception("Unable to update table: ".join(",",self::$pdo->errorInfo()));
				}
			}
		}

		/**
		 * Drop table if it exists.
		 */
		public final static function uninstall() {
			self::init();

			$table=self::$classes[get_called_class()]["table"];

			$dropStatement=self::$pdo->query("DROP TABLE IF EXISTS $table");
			if (!$dropStatement)
				throw new Exception("Unable to drop table: ".join(",",self::$pdo->errorInfo()));
		}

		/**
		 * Get value for primary key.
		 */
		private function getPrimaryKeyValue() {
			$conf=self::$classes[get_called_class()];

			if (!isset($this->$conf["primaryKey"]))
				return NULL;

			return $this->$conf["primaryKey"];
		}

		/**
		 * Get conf.
		 */
		private static function getConf() {
			self::init();
			return self::$classes[get_called_class()];
		}

		/**
		 * Save.
		 */
		public function save() {
			$conf=self::getConf();

			$pk=$this->getPrimaryKeyValue();
			$s="";

			if ($pk)
				$s.="UPDATE $conf[table] SET ";

			else
				$s.="INSERT INTO $conf[table] SET ";

			$first=TRUE;
			foreach ($conf["fields"] as $field=>$declaration)
				if ($field!=$conf["primaryKey"]) {
					if (!$first)
						$s.=", ";

					$s.="$field=:$field";
					$first=FALSE;
				}

			if ($pk)
				$s.=" WHERE $conf[primaryKey]=:$conf[primaryKey]";

			$statement=self::$pdo->prepare($s);

			if (!$statement)
				throw new Exception("can't create statement");

			foreach ($conf["fields"] as $field=>$declaration)
				if ($pk || $field!=$conf["primaryKey"])
					$statement->bindParam($field,$this->$field);

			$res=$statement->execute();

			if (!$res)
				throw new Exception("Unable to run query: ".join(",",$statement->errorInfo()));

			if (!$this->getPrimaryKeyValue())
				$this->$conf["primaryKey"]=self::$pdo->lastInsertId();
		}

		/**
		 * Delete this item.
		 */
		public static final function delete() {
			self::init();
			$conf=self::$classes[get_called_class()];

			if (!$this->getPrimaryKeyValue())
				throw new Exception("Can't delete, there is no id");

			$statement=self::$pdo->prepare("DELETE FROM $conf[table] WHERE $conf[primaryKey]=:id");
			$res=$statement->execute(array(
				"id"=>$this->getPrimaryKeyValue()
			));

			if (!$res)
				throw new Exception("Unable to run query: ".join(",",$statement->errorInfo()));

			unset($this->$conf["primaryKey"]);
		}

		/**
		 * Find all by query.
		 */
		public static final function findAllByQuery($query, $params=array()) {
			$conf=self::getConf();

			$query=str_replace(":table",self::getFullTableName(),$query);

			$statement=self::$pdo->prepare($query);
			if (!$statement)
				throw new Exception("Unable to create query");

			if (!$statement->execute($params))
				throw new Exception("Unable to run query: ".join(",",$statement->errorInfo()));

			$class=get_called_class();
			$res=array();

			do {
				$o=new $class;
				$statement->setFetchMode(PDO::FETCH_INTO,$o);
				$o=$statement->fetch();

				if ($o)
					$res[]=$o;
			} while ($o);

			return $res;
		}

		/**
		 * Find one by query.
		 */
		public static final function findOneByQuery($query, $params=array()) {
			$all=self::findAllByQuery($query,$params);

			if (!sizeof($all))
				return NULL;

			return $all[0];
		}

		/**
		 * Find all.
		 */
		public static final function findAll() {
			return self::findAllByQuery("SELECT * FROM :table");
		}

		/**
		 * Find all by value.
		 */
		public static final function findAllBy($field, $value) {
			return self::findAllByQuery(
				"SELECT * FROM :table WHERE $field=:value",
				array(
					"value"=>$value
				)
			);
		}

		/**
		 * Find one by value.
		 */
		public static final function findOneBy($field, $value) {
			$res=self::findAllBy($field,$value);

			if (!sizeof($res))
				return NULL;

			return $res[0];
		}

		/**
		 * Find one by id.
		 */
		public static final function findOne($id) {
			$conf=self::getConf();

			return self::findOneBy($conf["primaryKey"],$id);
		}
	}