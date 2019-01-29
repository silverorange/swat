<?php

/**
 * Database helper class
 *
 * Static convenience methods for working with a database.
 *
 * @package   SwatDB
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDB extends SwatObject
{
	// {{{ protected static properties

	protected static $query_count = 0;
	protected static $debug = false;
	protected static $debug_info = array();
	protected static $debug_wrapper_depth = 0;

	// }}}
	// {{{ public static function setDebug()

	/**
	 * Sets the debug mode used by SwatDB
	 *
	 * @param boolean $debug optional. Whether or not to display SQL queries.
	 *                        Defaults to true.
	 */
	public static function setDebug($debug = true)
	{
		self::$debug = (bool) $debug;
	}

	// }}}
	// {{{ public static function connect()

	/**
	 * Connects to a database
	 *
	 * Convenience method to connect to a database and get a driver instance
	 * for that database.
	 *
	 * @param string $dsn the DSN to which to connect.
	 *
	 * @return MDB2_Driver_Common $db the database connection.
	 *
	 * @throws SwatDBException
	 */
	public static function connect($dsn)
	{
		$db = MDB2::connect($dsn);

		if (PEAR::isError($db)) {
			throw new SwatDBException($db);
		}

		return $db;
	}

	// }}}
	// {{{ public static function query()

	/**
	 * Performs an SQL query
	 *
	 * @param MDB2_Driver_Common            $db      the database connection.
	 * @param string                        $sql     the SQL to execute.
	 * @param string|SwatDBRecordsetWrapper $wrapper optional. The object or
	 *                                               name of class with which
	 *                                               to wrap the result set. If
	 *                                               not specified,
	 *                                               {@link SwatDBDefaultRecordsetWrapper}
	 *                                               is used. Specify
	 *                                               <kbd>null</kbd> to return
	 *                                               an unwrapped MDB2 result.
	 * @param array                         $types   optional. An array of MDB2 datatypes
	 *                                               for the columns of the
	 *                                               result set.
	 *
	 * @return mixed A recordset containing the query result. If <i>$wrapper</i>
	 *               is specified as null, a MDB2_Result_Common object is
	 *               returned.
	 *
	 * @throws SwatDBException
	 */
	public static function query(
		$db,
		$sql,
		$wrapper = 'SwatDBDefaultRecordsetWrapper',
		$types = null
	) {
		$mdb2_types = $types === null ? true : $types;

		$rs = self::executeQuery($db, 'query', array(
			$sql,
			$mdb2_types,
			true,
			false
		));

		// Wrap results. Do it here instead of in MDB2 so we can wrap using
		// an existing object instance.
		if (is_string($wrapper)) {
			$rs = new $wrapper($rs);
		} elseif ($wrapper instanceof SwatDBRecordsetWrapper) {
			$wrapper->initializeFromResultSet($rs);
			$rs = $wrapper;
		}

		return $rs;
	}

	// }}}
	// {{{ public static function exec()

	/**
	 * Execute a data manipulation SQL statement
	 *
	 * Convenience method for MDB2::exec().
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 * @param string $sql The SQL to execute.
	 *
	 * @return integer Number of affected rows.
	 *
	 * @throws SwatDBException
	 */
	public static function exec($db, $sql)
	{
		return self::executeQuery($db, 'exec', array($sql));
	}

	// }}}
	// {{{ public static function updateColumn()

	/**
	 * Update a column
	 *
	 * Convenience method to update a single database field for one or more
	 * rows. One convenient use of this method is for processing {@link
	 * SwatAction}s
	 * that change a single database field.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $field The name of the database field to update. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then integer is assummed for this
	 *        field.
	 *
	 * @param mixed $value The value to store in database field $field. The
	 *        type should correspond to the type of $field.
	 *
	 * @param string $id_field The name of the database field that contains the
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param array $ids An array of identifiers corresponding to the database
	 *        rows to be updated. The type of the individual identifiers should
	 *        correspond to the type of $id_field.
	 *
	 * @param string $where An optional additional where clause.
	 *
	 * @return integer the number of rows updated.
	 *
	 * @throws SwatDBException
	 */
	public static function updateColumn(
		$db,
		$table,
		$field,
		$value,
		$id_field,
		$ids,
		$where = null
	) {
		$ids = self::initArray($ids);

		if (count($ids) == 0) {
			return;
		}

		$field = new SwatDBField($field, 'integer');
		$id_field = new SwatDBField($id_field, 'integer');

		$sql = 'update %s set %s = %s where %s in (%s) %s';

		foreach ($ids as &$id) {
			$id = $db->quote($id, $id_field->type);
		}

		$id_list = implode(',', $ids);

		$where = $where === null ? '' : 'and ' . $where;

		$sql = sprintf(
			$sql,
			$table,
			$field->name,
			$db->quote($value, $field->type),
			$id_field->name,
			$id_list,
			$where
		);

		return self::exec($db, $sql);
	}

	// }}}
	// {{{ public static function queryColumn()

	/**
	 * Query a column
	 *
	 * Convenience method to query for values in a single database column.
	 * One convenient use of this method is for loading values from a binding
	 * table.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $field The name of the database field to query. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then integer is assummed for this
	 *        field.
	 *
	 * @param string $id_field The name of the database field that contains the
	 *        the id.  If not null this will be used to construct a where clause
	 *        to limit results. Can be given in the form type:name where type is
	 *        a standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param mixed $id The value to look for in the $id_field. The type should
	 *        correspond to the type of $id_field.
	 *
	 * @return array An associative array of $id_field => $field
	 *
	 * @throws SwatDBException
	 */
	public static function queryColumn(
		$db,
		$table,
		$field,
		$id_field = null,
		$id = 0
	) {
		$field = new SwatDBField($field, 'integer');

		if ($id_field == null) {
			$sql = 'select %s from %s';
			$sql = sprintf($sql, $field->name, $table);
		} else {
			$id_field = new SwatDBField($id_field, 'integer');
			$sql = 'select %s from %s where %s = %s';
			$sql = sprintf(
				$sql,
				$field->name,
				$table,
				$id_field->name,
				$db->quote($id, $id_field->type)
			);
		}

		$values = self::executeQuery($db, 'queryCol', array(
			$sql,
			$field->type
		));

		return $values;
	}

	// }}}
	// {{{ public static function queryOne()

	/**
	 * Query a single value
	 *
	 * Convenience method to query a single value in a single database column.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 * @param string $sql The SQL to execute.
	 * @param string $type Optional MDB2 datatype for the result.
	 *
	 * @return mixed The value queried for a single result. Null when there are
	 *         no results.
	 *
	 * @throws SwatDBException
	 */
	public static function queryOne($db, $sql, $type = null)
	{
		$mdb2_type = $type === null ? true : $type;
		return self::executeQuery($db, 'queryOne', array($sql, $mdb2_type));
	}

	// }}}
	// {{{ public static function queryRow()

	/**
	 * Query a single row
	 *
	 * Convenience method to query for a single row from a database table.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 * @param string $sql The SQL to execute.
	 * @param array $types Optional array of MDB2 datatypes for the result.
	 *
	 * @return Object A row object, or null.
	 *
	 * @throws SwatDBException
	 */
	public static function queryRow($db, $sql, $types = null)
	{
		$mdb2_types = $types === null ? true : $types;

		$row = self::executeQuery($db, 'queryRow', array(
			$sql,
			$mdb2_types,
			MDB2_FETCHMODE_OBJECT
		));

		return $row;
	}

	// }}}
	// {{{ public static function queryOneFromTable()

	/**
	 * Query a single value from a specified table and column
	 *
	 * Convenience method to query a single value in a single database column.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $field The name of the database field to query. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then integer is assummed for this
	 *        field.
	 *
	 * @param string $id_field The name of the database field that contains the
	 *        the id.  If not null this will be used to construct a where clause
	 *        to limit results. Can be given in the form type:name where type is
	 *        a standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param mixed $id The value to look for in the $id_field. The type should
	 *        correspond to the type of $id_field.
	 *
	 * @return mixed The value queried for a single result.
	 *
	 * @throws SwatDBException
	 */
	public static function queryOneFromTable(
		$db,
		$table,
		$field,
		$id_field = null,
		$id = 0
	) {
		$field = new SwatDBField($field, 'integer');

		if ($id_field == null) {
			$sql = 'select %s from %s';
			$sql = sprintf($sql, $field->name, $table);
		} else {
			$id_field = new SwatDBField($id_field, 'integer');
			$sql = 'select %s from %s where %s = %s';
			$sql = sprintf(
				$sql,
				$field->name,
				$table,
				$id_field->name,
				$db->quote($id, $id_field->type)
			);
		}

		$value = self::queryOne($db, $sql, $field->type);

		return $value;
	}

	// }}}
	// {{{ public static function queryRowFromTable()

	/**
	 * Query a single row from a specified table and column
	 *
	 * Convenience method to query for a single row from a database table.
	 * One convenient use of this method is for loading data on an edit page.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param array $fields An array of fields to be queried. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then text is assummed.
	 *
	 * @param string $id_field The name of the database field that contains the
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param mixed $id The value to look for in the id field column. The
	 *        type should correspond to the type of $field.
	 *
	 * @return Object A row object.
	 *
	 * @throws SwatDBException
	 */
	public static function queryRowFromTable(
		$db,
		$table,
		$fields,
		$id_field,
		$id
	) {
		self::initFields($fields);
		$id_field = new SwatDBField($id_field, 'integer');
		$sql = 'select %s from %s where %s = %s';
		$field_list = implode(',', self::getFieldNameArray($fields));

		$sql = sprintf(
			$sql,
			$field_list,
			$table,
			$id_field->name,
			$db->quote($id, $id_field->type)
		);

		$rs = self::query($db, $sql, null);
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		if (MDB2::isError($row)) {
			throw new SwatDBException($row);
		}

		return $row;
	}

	// }}}
	// {{{ public static function executeStoredProc()

	/**
	 * Performs a stored procedure
	 *
	 * @param MDB2_Driver_Common            $db      the database connection.
	 * @param string                        $proc    the name of the stored
	 *                                               procedure to execute.
	 * @param mixed                         $params  the parameters to pass to
	 *                                               the stored procedure. Use
	 *                                               an array for more than one
	 *                                               parameter.
	 * @param string|SwatDBRecordsetWrapper $wrapper optional. The object or
	 *                                               name of class with which
	 *                                               to wrap the result set. If
	 *                                               not specified,
	 *                                               {@link SwatDBDefaultRecordsetWrapper}
	 *                                               is used. Specify
	 *                                               <kbd>null</kbd> to return
	 *                                               an unwrapped MDB2 result.
	 * @param array                         $types   optional. An array of MDB2 datatypes
	 *                                               for the columns of the
	 *                                               result set.
	 *
	 * @return mixed A recordset containing the query result. If <i>$wrapper</i>
	 *               is specified as null, a MDB2_Result_Common object is
	 *               returned.
	 *
	 * @throws SwatDBException
	 */
	public static function executeStoredProc(
		$db,
		$proc,
		$params,
		$wrapper = 'SwatDBDefaultRecordsetWrapper',
		$types = null
	) {
		if (!is_array($params)) {
			$params = array($params);
		}

		$mdb2_types = $types === null ? true : $types;

		$db->loadModule('Function');
		$rs = $db->function->executeStoredProc(
			$proc,
			$params,
			$mdb2_types,
			true,
			false
		);

		if (MDB2::isError($rs)) {
			throw new SwatDBException($rs);
		}

		// Wrap results. Do it here instead of in MDB2 so we can wrap using
		// an existing object instance.
		if (is_string($wrapper)) {
			$rs = new $wrapper($rs);
		} elseif ($wrapper instanceof SwatDBRecordsetWrapper) {
			$wrapper->initializeFromResultSet($rs);
			$rs = $wrapper;
		}

		return $rs;
	}

	// }}}
	// {{{ public static function executeStoredProcOne()

	/**
	 * Execute a stored procedure that returns a single value
	 *
	 * Convenience method to execute a stored procedure that returns a single
	 * value.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $proc The name of the stored procedure to execute.
	 *
	 * @param mixed $params The parameters to pass to the stored procedure.
	 *        Use an array for more than one parameter.
	 *
	 * @return mixed The value returned by the stored procedure.
	 *
	 * @throws SwatDBException
	 */
	public static function executeStoredProcOne($db, $proc, $params)
	{
		if (!is_array($params)) {
			$params = array($params);
		}

		$rs = self::executeStoredProc($db, $proc, $params);
		$row = $rs->getFirst();
		return current($row);
	}

	// }}}
	// {{{ public static function updateBinding()

	/**
	 * Update a binding table
	 *
	 * Convenience method to update rows in a binding table. It will delete
	 * and insert rows as necessary.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The binding table to update.
	 *
	 * @param string $id_field The name of the binding table field that contains
	 *        the fixed value.  Can be given in the form type:name where type is
	 *        a standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param mixed $id The value to store in the $id_field. The type should
	 *        correspond to the type of $id_field.
	 *
	 * @param string $value_field The name of the binding table field that
	 *        contains the values from the bound table.  Can be given in the
	 *        form type:name where type is a standard MDB2 datatype. If type is
	 *        ommitted, then integer is assummed for this field.
	 *
	 * @param array $values An array of values that should be stored in the
	 *        $value_field. The type of the individual values should
	 *        correspond to the type of $value_field.
	 *
	 * @param string $bound_table The table bound through the binding table.
	 *
	 * @param string $bound_field The database field in the bound table that the
	 *        binding table references.
	 *
	 * @throws SwatDBException
	 */
	public static function updateBinding(
		$db,
		$table,
		$id_field,
		$id,
		$value_field,
		$values,
		$bound_table,
		$bound_field
	) {
		$id_field = new SwatDBField($id_field, 'integer');
		$value_field = new SwatDBField($value_field, 'integer');
		$bound_field = new SwatDBField($bound_field, 'integer');

		$values = self::initArray($values);

		$delete_sql = 'delete from %s where %s = %s';

		$delete_sql = sprintf(
			$delete_sql,
			$table,
			$id_field->name,
			$db->quote($id, $id_field->type)
		);

		if (count($values)) {
			foreach ($values as &$value) {
				$value = $db->quote($value, $value_field->type);
			}

			$value_list = implode(',', $values);

			$insert_sql =
				'insert into %s (%s, %s) select %s, %s from %s ' .
				'where %s not in (select %s from %s where %s = %s) and %s in (%s)';

			$insert_sql = sprintf(
				$insert_sql,
				$table,
				$id_field->name,
				$value_field->name,
				$db->quote($id, $id_field->type),
				$bound_field->name,
				$bound_table,
				$bound_field->name,
				$value_field->name,
				$table,
				$id_field->name,
				$db->quote($id, $id_field->type),
				$bound_field->name,
				$value_list
			);

			$delete_sql .= sprintf(
				' and %s not in (%s)',
				$value_field->name,
				$value_list
			);
		}

		$transaction = new SwatDBTransaction($db);
		try {
			if (count($values)) {
				self::exec($db, $insert_sql);
			}

			self::exec($db, $delete_sql);
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
		$transaction->commit();
	}

	// }}}
	// {{{ public static function insertRow()

	/**
	 * Insert a row
	 *
	 * Convenience method to insert a single database row. One convenient use
	 * of this method is for saving data on an edit page.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to update.
	 *
	 * @param array $fields An array of fields to be updated. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then text is assummed.
	 *
	 * @param array $values An associative array of values to store in the
	 *        database.  The array keys should correspond to field names.
	 *        The type of the individual values should correspond to the
	 *        field type.
	 *
	 * @param string $id_field The name of the database field that contains an
	 *        identifier of row to be updated. Can be given in the form
	 *        type:name where type is a standard MDB2 datatype. If type is
	 *        ommitted, then integer is assummed for this field.
	 *        If $id_field is set, the value in the $id_field column of
	 *        the inserted row is returned.
	 *
	 * @return mixed If $id_field is set, the value in the $id_field column of
	 *        the inserted row is returned.
	 *
	 * @throws SwatDBException
	 */
	public static function insertRow(
		$db,
		$table,
		$fields,
		$values,
		$id_field = null
	) {
		self::initFields($fields);

		$ret = null;

		$transaction = new SwatDBTransaction($db);
		try {
			if (count($fields) === 0) {
				$sql = sprintf('insert into %s values (default)', $table);
			} else {
				$field_list = implode(',', self::getFieldNameArray($fields));

				$values_in_order = array();

				foreach ($fields as &$field) {
					$value = isset($values[$field->name])
						? $values[$field->name]
						: null;

					$values_in_order[] = $db->quote($value, $field->type);
				}

				$value_list = implode(',', $values_in_order);

				$sql = sprintf(
					'insert into %s (%s) values (%s)',
					$table,
					$field_list,
					$value_list
				);
			}

			$result = self::exec($db, $sql);

			if ($id_field !== null) {
				$ret = self::getFieldMax($db, $table, $id_field);
			}
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
		$transaction->commit();

		return $ret;
	}

	// }}}
	// {{{ public static function updateRow()

	/**
	 * Update a row
	 *
	 * Convenience method to update multiple fields of a single database row.
	 * One convenient use of this method is for save data on an edit page.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to update.
	 *
	 * @param array $fields An array of fields to be updated. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then text is assummed.
	 *
	 * @param array $values An associative array of values to store in the
	 *        database.  The array keys should correspond to field names.
	 *        The type of the individual values should correspond to the
	 *        field type.
	 *
	 * @param string $id_field The name of the database field that contains an
	 *        identifier of row to be updated. Can be given in the form
	 *        type:name where type is a standard MDB2 datatype. If type is
	 *        ommitted, then integer is assummed for this field.
	 *
	 * @param mixed $id The value to look for in the $id_field column. The
	 *        type should correspond to the type of $field.
	 *
	 * @throws SwatDBException
	 */
	public static function updateRow(
		$db,
		$table,
		$fields,
		$values,
		$id_field,
		$id
	) {
		self::initFields($fields);
		$id_field = new SwatDBField($id_field, 'integer');
		$sql = 'update %s set %s where %s = %s';
		$updates = array();

		foreach ($fields as &$field) {
			$value = isset($values[$field->name])
				? $values[$field->name]
				: null;

			$updates[] = sprintf(
				'%s = %s',
				$field->name,
				$db->quote($value, $field->type)
			);
		}

		$update_list = implode(',', $updates);

		$sql = sprintf(
			$sql,
			$table,
			$update_list,
			$id_field->name,
			$db->quote($id, $id_field->type)
		);

		self::exec($db, $sql);
	}

	// }}}
	// {{{ public static function deleteRow()

	/**
	 * Delete a row
	 *
	 * Convenience method to delete a single database row.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to delete from.
	 *
	 * @param string $id_field The name of the database field that contains an
	 *        identifier of row to be deleted. Can be given in the form
	 *        type:name where type is a standard MDB2 datatype. If type is
	 *        ommitted, then integer is assummed for this field.
	 *
	 * @param mixed $id The value to look for in the $id_field column. The
	 *        type should correspond to the type of $field.
	 *
	 * @throws SwatDBException
	 */
	public static function deleteRow($db, $table, $id_field, $id)
	{
		$id_field = new SwatDBField($id_field, 'integer');
		$sql = 'delete from %s where %s = %s';

		$sql = sprintf(
			$sql,
			$table,
			$id_field->name,
			$db->quote($id, $id_field->type)
		);

		self::exec($db, $sql);
	}

	// }}}
	// {{{ public static function getOptionArray()

	/**
	 * Query for an option array
	 *
	 * Convenience method to query for a set of options, each consisting of
	 * an id and a title. The returned option array in the form of
	 * $id => $title can be passed directly to other classes, such as
	 * {@link SwatFlydown} for example.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $title_field The name of the database field to query for
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is
	 *        assummed for this field.
	 *
	 * @param string $id_field The name of the database field to query for
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param string $order_by_clause Optional comma deliminated list of
	 *        database field names to use in the <i>order by</i> clause.
	 *        Do not include "order by" in the string; only include the list
	 *        of field names. Pass null to skip over this paramater.
	 *
	 * @param string $where_clause Optional <i>where</i> clause to limit the
	 *        returned results.  Do not include "where" in the string; only
	 *        include the conditionals.
	 *
	 * @return array An array in the form of $id => $title.
	 *
	 * @throws SwatDBException
	 */
	public static function getOptionArray(
		$db,
		$table,
		$title_field,
		$id_field,
		$order_by_clause = null,
		$where_clause = null
	) {
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');

		$sql = 'select %s, %s from %s';
		$sql = sprintf($sql, $id_field->name, $title_field->name, $table);

		if ($where_clause != null) {
			$sql .= ' where ' . $where_clause;
		}

		if ($order_by_clause != null) {
			$sql .= ' order by ' . $order_by_clause;
		}

		$rs = self::query($db, $sql, null);

		$options = array();

		while (($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))) {
			if (MDB2::isError($row)) {
				throw new SwatDBException($row);
			}

			$title_field_name = $title_field->name;
			$id_field_name = $id_field->name;
			$options[$row->$id_field_name] = $row->$title_field_name;
		}

		return $options;
	}

	// }}}
	// {{{ public static function getCascadeOptionArray()

	/**
	 * Query for an option array cascaded by a field
	 *
	 * Convenience method to query for a set of options, each consisting of
	 * an id, title, and a group-by field. The returned option array in the form of
	 * $cascade => array($id => $title, $id => $title) can be passed directly to
	 * other classes, such as {@link SwatCascade} for example.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $title_field The name of the database field to query for
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is
	 *        assummed for this field.
	 *
	 * @param string $id_field The name of the database field to query for
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param string $cascade_field The name of the database field to cascade
	 *        the options by. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param string $order_by_clause Optional comma deliminated list of
	 *        database field names to use in the <i>order by</i> clause.
	 *        Do not include "order by" in the string; only include the list
	 *        of field names. Pass null to skip over this paramater.
	 *
	 * @param string $where_clause Optional <i>where</i> clause to limit the
	 *        returned results.  Do not include "where" in the string; only
	 *        include the conditionals.
	 *
	 * @return array An array in the form of $id => $title.
	 *
	 * @throws SwatDBException
	 */
	public static function getCascadeOptionArray(
		$db,
		$table,
		$title_field,
		$id_field,
		$cascade_field,
		$order_by_clause = null,
		$where_clause = null
	) {
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');
		$cascade_field = new SwatDBField($cascade_field, 'integer');

		$sql = 'select %s, %s, %s from %s';
		$sql = sprintf(
			$sql,
			$id_field->name,
			$title_field->name,
			$cascade_field->name,
			$table
		);

		if ($where_clause !== null) {
			$sql .= ' where ' . $where_clause;
		}

		$sql .= ' order by ' . $cascade_field->name;
		if ($order_by_clause !== null) {
			$sql .= ', ' . $order_by_clause;
		}

		$rs = self::query($db, $sql, null);

		$options = array();
		$current = null;
		$title_field_name = $title_field->name;
		$id_field_name = $id_field->name;
		$cascade_field_name = $cascade_field->name;

		while (($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))) {
			if (MDB2::isError($row)) {
				throw new SwatDBException($row);
			}

			if ($row->$cascade_field_name != $current) {
				$current = $row->$cascade_field_name;
				$options[$current] = array();
			}

			$options[$current][$row->$id_field_name] = $row->$title_field_name;
		}

		return $options;
	}

	// }}}
	// {{{ public static function getGroupedOptionArray()

	/**
	 * Queries for a grouped option array
	 *
	 * Convenience method to query a grouped list of {@link SwatDataTreeNode}
	 * objects used for things like {@link SwatCheckboxList} where checkboxes
	 * are grouped together under a title.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $title_field The name of the database field to query for
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is
	 *        assummed for this field.
	 *
	 * @param string $id_field The name of the database field to query for
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is
	 *        assummed for this field.
	 *
	 * @param string $group_table The database table that the group titles come
	 *        from.
	 *
	 * @param string $group_idfield The name of the database field to query for
	 *        the id of the $group_table. Can be given in the form type:name
	 *        where type is a standard MDB2 datatype. If type is ommitted, then
	 *        integer is assummed for this field.
	 *
	 * @param string $group_title_field The name of the database field to query
	 *        for the group title. Can be given in the form type:name where
	 *        type is a standard MDB2 datatype. If type is ommitted, then text
	 *        is assummed for this field.
	 *
	 * @param string $group_field The name of the database field in $table that
	 *        links with the $group_idfield. Can be given in the form type:name
	 *        where type is a standard MDB2 datatype. If type is ommitted, then
	 *        integer is assummed for this field.
	 *
	 * @param string $order_by_clause Optional comma deliminated list of
	 *        database field names to use in the <i>order by</i> clause.
	 *        Do not include "order by" in the string; only include the list
	 *        of field names. Pass null to skip over this paramater.
	 *
	 * @param string $where_clause Optional <i>where</i> clause to limit the
	 *        returned results.  Do not include "where" in the string; only
	 *        include the conditionals.
	 * @param SwatDataTreeNode $tree a tree to add nodes to. If no tree is
	 *                                specified, nodes are added to a new
	 *                                empty tree.
	 *
	 * @return SwatDataTreeNode a tree composed of {@link SwatDataTreeNode}
	 *                           objects.
	 *
	 * @throws SwatDBException
	 */
	public static function getGroupedOptionArray(
		$db,
		$table,
		$title_field,
		$id_field,
		$group_table,
		$group_title_field,
		$group_id_field,
		$group_field,
		$order_by_clause = null,
		$where_clause = null,
		$tree = null
	) {
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');
		$group_title_field = new SwatDBField($group_title_field, 'text');
		$group_id_field = new SwatDBField($group_id_field, 'integer');
		$group_field = new SwatDBField($group_field, 'text');

		$sql = 'select %s as id, %s as title, %s as group_title, %s as group_id
			from %s';

		$sql = sprintf(
			$sql,
			"{$table}.{$id_field->name}",
			"{$table}.{$title_field->name}",
			"{$group_table}.{$group_title_field->name}",
			"{$group_table}.{$group_id_field->name}",
			$table
		);

		$sql .= ' inner join %s on %s = %s';
		$sql = sprintf(
			$sql,
			$group_table,
			"{$group_table}.{$group_id_field->name}",
			"{$table}.{$group_field->name}"
		);

		if ($where_clause != null) {
			$sql .= ' where ' . $where_clause;
		}

		if ($order_by_clause != null) {
			$sql .= ' order by ' . $order_by_clause;
		}

		$rs = self::query($db, $sql, null);

		$options = array();

		if ($tree !== null && $tree instanceof SwatDataTreeNode) {
			$base_parent = $tree;
		} else {
			$base_parent = new SwatDataTreeNode(null, Swat::_('Root'));
		}

		$current_group = null;

		while (($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))) {
			if ($current_group !== $row->group_id) {
				$current_parent = new SwatDataTreeNode(null, $row->group_title);
				$base_parent->addChild($current_parent);
				$current_group = $row->group_id;
			}

			$current_parent->addChild(
				new SwatDataTreeNode($row->id, $row->title)
			);
		}

		return $base_parent;
	}

	// }}}
	// {{{ public static function getFieldMax()

	/**
	 * Get max field value
	 *
	 * Convenience method to grab the max value from a single field.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to update.
	 *
	 * @param string $field The field to be return the max value of. Can be
	 *        given in the form type:name where type is a standard MDB2
	 *        datatype. If type is ommitted, then text is assummed.
	 *
	 * @return mixed The max value of field specified.
	 *
	 * @throws SwatDBException
	 */
	public static function getFieldMax($db, $table, $field)
	{
		$field = new SwatDBField($field, 'integer');

		$sql = sprintf(
			'select max(%s) as %s from %s',
			$field->name,
			$field->name,
			$table
		);

		return self::queryOne($db, $sql);
	}

	// }}}
	// {{{ public static function equalityOperator()

	/**
	 * Get proper conditional operator
	 *
	 * Convenience method to return proper operators for database values that
	 * may be null.
	 *
	 * @param mixed $value The value to check for null on
	 *
	 * @param boolean $neg Whether to return the operator for a negative
	 *        comparison
	 *
	 * @return string SQL operator
	 */
	public static function equalityOperator($value, $neg = false)
	{
		if ($value === null && $neg) {
			return 'is not';
		} elseif ($value === null) {
			return 'is';
		} elseif ($neg) {
			return '!=';
		} else {
			return '=';
		}
	}

	// }}}
	// {{{ public static function getDataTree()

	/**
	 * Get a tree of data nodes
	 *
	 * Convenience method to take a structured query with each row consisting of
	 * an id, levelnum, and a title, and turning it into a tree of
	 * {@link SwatDataTreeNode} objects. The returned option array in the form
	 * of a collection of {@link SwatDataTreeNode} objects can be used by other
	 * classes, such as {@link SwatTreeFlydown} and
	 * {@link @SwatChecklistTree}.
	 *
	 * @param MDB2_Driver_Common $rs The MDB2 result set, usually the
	 * 	result of a stored procedure. Must be wrapped in {@link
	 * 	SwatDBRecordsetWrapper}.
	 *
	 * @param string $title_field_name The name of the database field
	 * 	representing the title
	 *
	 * @param string $idfield_field_name The name of the database field
	 * 	representing the id
	 *
	 * @param string $level_field_name the name of the database field
	 *                                  representing the tree level.
	 * @param SwatDataTreeNode $tree an optional tree to add nodes to. If no
	 *                                   tree is specified, nodes are added to
	 *                                   a new empty tree.
	 *
	 * @return SwatDataTreeNode a tree composed of
	 *                              {@link SwatDataTreeNode} objects.
	 *
	 * @throws SwatDBException
	 */
	public static function getDataTree(
		$rs,
		$title_field_name,
		$id_field_name,
		$level_field_name,
		$tree = null
	) {
		$stack = array();
		if ($tree !== null && $tree instanceof SwatDataTreeNode) {
			$current_parent = $tree;
		} else {
			$current_parent = new SwatDataTreeNode('', Swat::_('Root'));
		}

		$base_parent = $current_parent;
		array_push($stack, $current_parent);
		$last_node = $current_parent;

		foreach ($rs as $row) {
			$title = $row->$title_field_name;
			$id = $row->$id_field_name;
			$level = $row->$level_field_name;

			if ($level > count($stack)) {
				array_push($stack, $current_parent);
				$current_parent = $last_node;
			} elseif ($level < count($stack)) {
				$current_parent = array_pop($stack);
			}

			$last_node = new SwatDataTreeNode($id, $title);
			$current_parent->addChild($last_node);
		}

		return $base_parent;
	}

	// }}}
	// {{{ public static function implodeSelection()

	/**
	 * Implodes a view selection object
	 *
	 * Each item in the view is quoted using the specified type.
	 *
	 * @param MDB2_Driver_Common $db the database connection to use to implode
	 *                                the view.
	 * @param SwatViewSelection $selection the selection to implode.
	 * @param string $type optional. The datatype to use. Must be a valid MDB2
	 *                      datatype. If unspecified, 'integer' is used.
	 *
	 * @return string the imploded view ready for inclusion in an SQL statement.
	 */
	public static function implodeSelection(
		MDB2_Driver_Common $db,
		SwatViewSelection $selection,
		$type = 'integer'
	) {
		$quoted_ids = array();
		foreach ($selection as $id) {
			$quoted_ids[] = $db->quote($id, $type);
		}

		return implode(',', $quoted_ids);
	}

	// }}}
	// {{{ private static function executeQuery()

	private static function executeQuery($db, $method, array $args)
	{
		self::$query_count++;
		self::debugStart(current($args)); // sql is always the first arg
		$ret = call_user_func_array(array($db, $method), $args);
		self::debugEnd();

		if (MDB2::isError($ret)) {
			throw new SwatDBException($ret);
		}

		return $ret;
	}

	// }}}
	// {{{ private static function getFieldNameArray()

	private static function getFieldNameArray($fields)
	{
		if (count($fields) == 0) {
			return;
		}

		$names = array();

		foreach ($fields as &$field) {
			$names[] = $field->name;
		}

		return $names;
	}

	// }}}
	// {{{ private static function getFieldTypeArray()

	private static function getFieldTypeArray($fields)
	{
		if (count($fields) == 0) {
			return;
		}

		$types = array();

		foreach ($fields as &$field) {
			$types[] = $field->type;
		}

		return $types;
	}

	// }}}
	// {{{ private static function initFields()

	/**
	 * Transforms an array of text field identifiers ('type:name') into
	 * an array of SwatDBField objects.
	 *
	 * The array is passed by reference and modified in-place. Nothing is
	 * returned by this method.
	 *
	 * @param array $fields a reference to the array of field identifiers to
	 *                       transform.
	 */
	private static function initFields(&$fields)
	{
		if (count($fields) == 0) {
			// TODO: throw exception instead of returning
			return;
		}

		foreach ($fields as &$field) {
			$field = new SwatDBField($field, 'text');
		}
	}

	// }}}
	// {{{ private static function initArray()

	/**
	 * Noramlizes Iterator objects into simple arrays
	 *
	 * Checks a variable to see if it is an array or if it is an Iterator. If
	 * the variable is an Iterator, converts it to an array of values.
	 *
	 * @param array|Iterator $array the variable to normalize.
	 *
	 * @return array the normalized array
	 *
	 * @throws SwatDBException if the <i>$array</i> parameter is not an array
	 *                         or an Iterator.
	 */
	private static function initArray($array)
	{
		if (is_array($array)) {
			return $array;
		} elseif ($array instanceof Iterator) {
			$return = array();
			foreach ($array as $value) {
				$return[] = $value;
			}

			return $return;
		}

		throw new SwatDBException('Value is not an array');
	}

	// }}}
	// {{{ private static function debugStart()

	private static function debugStart($message)
	{
		// SWATDB_DEBUG is legacy, use SwatDB::setDebug() instead
		if (defined('SWATDB_DEBUG') || self::$debug) {
			$trace = debug_backtrace();

			// get first trace line that is not in the SwatDB package
			foreach ($trace as $entry) {
				if (
					!array_key_exists('class', $entry) ||
					strncmp($entry['class'], 'SwatDB', 6) !== 0
				) {
					break;
				}
			}

			$class = array_key_exists('class', $entry) ? $entry['class'] : null;

			$function = array_key_exists('function', $entry)
				? $entry['function']
				: null;

			ob_start();
			printf(
				"<strong>%s%s%s()</strong><br />\n",
				$class === null ? '' : $class,
				array_key_exists('type', $entry) ? $entry['type'] : '',
				$function === null ? '' : $function
			);

			echo $message;
			$debug_message = ob_get_clean();

			$debug_info = array();
			$debug_info['message'] = $debug_message;
			$debug_info['time'] = microtime(true) * 1000;
			$debug_info['depth'] = self::$debug_wrapper_depth;
			$debug_info['count'] = self::$query_count;
			self::$debug_info[] = $debug_info;

			self::$debug_wrapper_depth++;
		}
	}

	// }}}
	// {{{ private static function debugEnd()

	private static function debugEnd()
	{
		// SWATDB_DEBUG is legacy, use SwatDB::setDebug() instead
		if (defined('SWATDB_DEBUG') || self::$debug) {
			self::$debug_wrapper_depth--;

			if (self::$debug_wrapper_depth == 0) {
				$count = 0;
				$depth = 0;

				foreach (self::$debug_info as $info) {
					if ($info['depth'] < $depth) {
						echo str_repeat(
							'</blockquote>',
							$depth - $info['depth']
						);
					} elseif ($info['depth'] > $depth) {
						echo '<blockquote class="swat-db-debug">';
					} elseif ($info['depth'] == 0) {
						echo '<hr />';
					}

					echo "\n" . $info['message'] . "\n";

					$locale = SwatI18NLocale::get();
					printf(
						"<p>Query #%s</p>\n",
						$locale->formatNumber($info['count'])
					);

					if (count(self::$debug_info) > $count + 1) {
						$time = self::$debug_info[$count + 1]['time'];
					} else {
						$time = microtime(true) * 1000;
					}

					$ms = $time - $info['time'];

					printf(
						"<p><strong>%s ms</strong></p>\n",
						$locale->formatNumber($ms, 3)
					);

					if ($info['depth'] == 0 && count(self::$debug_info) > 1) {
						$ms =
							microtime(true) * 1000 -
							self::$debug_info[0]['time'];

						echo '<p><strong>';

						printf(
							Swat::_(
								'Total time: %s ms (includes ' .
									'queries within the wrapper)'
							),
							$locale->formatNumber($ms, 3)
						);

						echo '</strong></p>';
					}

					$count++;
					$depth = $info['depth'];
				}

				if ($depth > 0) {
					echo str_repeat('</blockquote>', $depth);
				}

				// reset debug info for the next query
				self::$debug_info = array();
			}
		}
	}

	// }}}
}
