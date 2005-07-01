<?php
// vim: set fdm=marker:

require_once 'MDB2.php';
require_once 'SwatDB/SwatDBField.php';
require_once 'SwatDB/SwatDBException.php';
require_once 'Swat/SwatTreeNode.php';

/**
 * Database helper class
 *
 * Static convenience methods for working with a database.
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDB
{
    // {{{ public static function query()

	/**
	 * Query a recordset
	 *
 	 * Convenience method to query.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 * @param string $sql The SQL to execute.
	 * @param array $types Optional array MDB2 datatypes for the recordset.
	 *
	 * @return MDB2_result_common A recordset containing the query result.
	 */
	public static function query($db, $sql, $types = null)
	{
		SwatDB::debug($sql);
		$rs = $db->query($sql, $types);

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		return $rs;
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
	 */
	public static function updateColumn($db, $table, $field, $value, $id_field,
		$ids)
	{
		if (count($ids) == 0)
			return;

		$field = new SwatDBField($field, 'integer');
		$id_field = new SwatDBField($id_field, 'integer');

		$sql = 'update %s set %s = %s where %s in (%s)';

		foreach ($ids as &$id)
			$id = $db->quote($id, $id_field->type);

		$id_list = implode(',', $ids);

		$sql = sprintf($sql, 
			$table,
			$field->name,
			$db->quote($value, $field->type),
			$id_field->name,
			$id_list);

		SwatDB::debug($sql);
		$rs = $db->query($sql);

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());
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
	 */
	public static function queryColumn($db, $table, $field, $id_field = null,
		$id = 0)
	{
		$field = new SwatDBField($field, 'integer');

		if ($id_field == null) {
			$sql = 'select %s from %s';
			$sql = sprintf($sql, $field->name, $table);
		} else {
			$id_field = new SwatDBField($id_field, 'integer');
			$sql = 'select %s from %s where %s = %s';
			$sql = sprintf($sql, $field->name, $table, $id_field->name, $id);
		}

		SwatDB::debug($sql);
		$values = $db->queryCol($sql, $field->type);

		if (MDB2::isError($values))
			throw new SwatDBException($values->getMessage());

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
	 */
	public static function queryOne($db, $table, $field, $id_field = null,
		$id = 0)
	{
		$field = new SwatDBField($field, 'integer');

		if ($id_field == null) {
			$sql = 'select %s from %s';
			$sql = sprintf($sql, $field->name, $table);
		} else {
			$id_field = new SwatDBField($id_field, 'integer');
			$sql = 'select %s from %s where %s = %s';
			$sql = sprintf($sql, $field->name, $table, $id_field->name, $id);
		}

		SwatDB::debug($sql);
		$value = $db->queryOne($sql, $field->type);

		if (MDB2::isError($value))
			throw new SwatDBException($value->getMessage());

		return $value;
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
	 * @param string $value_field The name of the binding table field that contains 
	 *        the values from the bound table.  Can be given in the form type:name 
	 *        where type is a standard MDB2 datatype. If type is ommitted, then 
	 *        integer is assummed for this field.
	 *
	 * @param array $values An array of values that should be stored in the 
	 *        $value_field. The type of the individual values should 
	 *        correspond to the type of $value_field.
	 *
	 * @param string $table The table bound through the binding table.
	 *
	 * @param string $id_field The database field in the bound table that the 
	 *        binding table references.
	 */
	public static function updateBinding($db, $table, $id_field, $id,
		$value_field, $values, $bound_table, $bound_field)
	{
		$id_field = new SwatDBField($id_field, 'integer');
		$value_field = new SwatDBField($value_field, 'integer');
		$bound_field = new SwatDBField($bound_field, 'integer');

		$delete_sql = 'delete from %s where %s = %s';

		$delete_sql = sprintf($delete_sql, 
			$table,
			$id_field->name,
			$db->quote($id, $id_field->type));

		if (count($values)) {

			foreach ($values as &$value)
				$value = $db->quote($value, $value_field->type);

			$value_list = implode(',', $values);

			$insert_sql = 'insert into %s (%s, %s) select %s, %s from %s '.
				'where %s not in (select %s from %s where %s = %s) and %s in (%s)';

			$insert_sql = sprintf($insert_sql, 
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
				$value_list);

			$delete_sql .= sprintf(' and %s not in (%s)',
				$value_field->name,
				$value_list);
		}

		SwatDB::debug($insert_sql);
		SwatDB::debug($delete_sql);

		$db->beginTransaction();

		if (count($values)) {
			$ret = $db->query($insert_sql);
			if (MDB2::isError($ret))
				throw new SwatDBException($ret->getMessage());
		}

		$rs = $db->query($delete_sql);
		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());
		
		$db->commit();

	}

	// }}}
	// {{{ public static function queryRow()

	/**
	 * Query a single row
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
	 */
	public static function queryRow($db, $table, $fields, $id_field, $id)
	{
		SwatDB::initFields($fields);
		$id_field = new SwatDBField($id_field, 'integer');
		$sql = 'select %s from %s where %s = %s';

		$field_list = implode(',', SwatDB::getFieldNameArray($fields));

		$sql = sprintf($sql,
			$field_list,
			$table,
			$id_field->name,
			$db->quote($id, $id_field->type));

		SwatDB::debug($sql);
		$rs = $db->query($sql, SwatDB::getFieldTypeArray($fields));
		
		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());
		
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		return $row;
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
	 *		  If $id_field is set, the value in the $id_field column of
	 *        the inserted row is returned.
	 *
	 * @return mixed If $id_field is set, the value in the $id_field column of
	 *        the inserted row is returned.
	 */
	public static function insertRow($db, $table, $fields, $values,
		$id_field = null)
	{
		SwatDB::initFields($fields);

		$ret = null;

		if ($id_field != null)
			$db->beginTransaction();

		$sql = 'insert into %s (%s) values (%s)';
		$field_list = implode(',', SwatDB::getFieldNameArray($fields));

		$values_in_order = array();

		foreach ($fields as &$field)
			$values_in_order[] = $db->quote($values[$field->name], $field->type);

		$value_list = implode(',', $values_in_order);

		$sql = sprintf($sql,
			$table,
			$field_list,
			$value_list);

		SwatDB::debug($sql);
		$rs = $db->query($sql);

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		if ($id_field != null) {
			$ret = SwatDB::getFieldMax($db, $table, $id_field);						
			$db->commit();
		}

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
	 */
	public static function updateRow($db, $table, $fields, $values, $id_field,
		$id)
	{

		SwatDB::initFields($fields);
		$id_field = new SwatDBField($id_field, 'integer');
		$sql = 'update %s set %s where %s = %s';
		$updates = array();

		foreach ($fields as &$field)
			$updates[] = $field->name.' = '.$db->quote($values[$field->name], $field->type);

		$update_list = implode(',', $updates);

		$sql = sprintf($sql,
			$table,
			$update_list,
			$id_field->name,
			$db->quote($id, $id_field->type));

		SwatDB::debug($sql);
		$rs = $db->query($sql);

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());
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
	 */
	public static function getOptionArray($db, $table, $title_field, $id_field,
		$order_by_clause = null, $where_clause = null)
	{
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');

		$sql = 'select %s, %s from %s';
		$sql = sprintf($sql, $id_field->name, $title_field->name, $table);

		if ($where_clause != null)
			$sql .= ' where '.$where_clause;

		if ($order_by_clause != null)
			$sql .= ' order by '.$order_by_clause;

		SwatDB::debug($sql);
		$rs = $db->query($sql, array($id_field->type, $title_field->type));

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		$options = array();

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
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
	 */
	public static function getCascadeOptionArray($db, $table, $title_field,
		$id_field, $cascade_field, $order_by_clause = null,
		$where_clause = null)
	{
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');
		$cascade_field = new SwatDBField($cascade_field, 'integer');

		$sql = 'select %s, %s, %s from %s';
		$sql = sprintf($sql, $id_field->name, $title_field->name,
			$cascade_field->name, $table);

		if ($where_clause !== null)
			$sql .= ' where '.$where_clause;

		$sql .= ' order by '.$cascade_field->name;
		if ($order_by_clause !== null)
			$sql.', '.$order_by_clause;

		SwatDB::debug($sql);

		$rs = $db->query($sql, array($id_field->type, $title_field->type,
			$cascade_field->type));

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		$options = array();
		$current = null;
		$title_field_name = $title_field->name;
		$id_field_name = $id_field->name;
		$cascade_field_name = $cascade_field->name;
		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			if ($row->$cascade_field_name != $current) {
				$current = $row->$cascade_field_name;
				$options[$current] = array();
			}
			
			$options[$current][$row->$id_field_name] = $row->$title_field_name;
		}

		return $options;
	}

	// }}}
    // {{{ public static function getGroupOptionArray()

    /**
	 * Query for a grouped option array
	 *
 	 * Convenience method to query a grouped list of {@link SwatTreeNode}s use
 	 * for things like {@link SwatCheckboxList} where checkboxes are grouped
 	 * together under a title.
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
     *        the id of the $group_table. Can be given in the form type:name where
	 *        type is a standard MDB2 datatype. If type is ommitted, then integer is
     *        assummed for this field.
	 *
	 * @param string $group_title_field The name of the database field to query for 
	 *        the group title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is 
	 *        assummed for this field.
	 *
	 * @param string $group_field The name of the database field in $table that
	 *        links with the $group_idfield. Can be given in the form type:name where
	 *        type is a standard MDB2 datatype. If type is ommitted, then integer is
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
	 * @return SwatTreeNode A tree hierarchy of {@link SwatTreeNode}s
	 */
	public static function getGroupedOptionArray($db, $table, $title_field,
		$id_field, $group_table, $group_title_field, $group_id_field,
		$group_field, $order_by_clause = null, $where_clause = null)
	{
		$title_field = new SwatDBField($title_field, 'text');
		$id_field = new SwatDBField($id_field, 'integer');
		$group_title_field = new SwatDBField($group_title_field, 'text');
		$group_id_field = new SwatDBField($group_id_field, 'integer');
		$group_field = new SwatDBField($group_field, 'text');

		$sql = 'select %s as id, %s as title, %s as group_title, %s as group_id from %s';
		$sql = sprintf($sql,
			"{$table}.{$id_field->name}",
			"{$table}.{$title_field->name}",
			"{$group_table}.{$group_title_field->name}",
			"{$group_table}.{$group_id_field->name}",
			$table);

		$sql.= ' inner join %s on %s = %s';
		$sql= sprintf($sql,
			$group_table,
			"{$group_table}.{$group_id_field->name}",
			"{$table}.{$group_field->name}");

		if ($where_clause != null)
			$sql .= ' where '.$where_clause;

		if ($order_by_clause != null)
			$sql .= ' order by '.$order_by_clause;
		
		SwatDB::debug($sql);
		$rs = $db->query($sql);

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		$options = array();

		$base_parent =  new SwatTreeNode();
		$current_group = null;

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			if ($current_group !== $row->group_id) {
				$current_parent = new SwatTreeNode(
					array('title' => $row->group_title));

				$base_parent->children[] = $current_parent;
				
				$current_group = $row->group_id;
			}

			$current_parent->children[] =
				new SwatTreeNode(array('title' => $row->title, 'value' => $row->id));
		}

		return $base_parent;
	}

	// }}}
    // {{{ public static function getTreeOptionArray()

	/**
	 * Query for an option tree array
	 *
 	 * Convenience method to query for a set of options, each consisting of
	 * an id, levelnum, and a title. The returned option array in the form of
	 * a collection of {@link SwatTreeNode}s to other classes, such as 
	 * SwatFlydown for example.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $sp Stored procedure/function to execute. Must return a 
	 *        recordset containing three columns in order: id, title, level.
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
	 * @param string $parent_field The name of the database field to query for 
	 *        the parent. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @return SwatTreeNode A tree hierarchy of {@link SwatTreeNode}s
	 */
	public static function getTreeOptionArray($db, $sp, $title_field,
		$id_field, $level_field)
	{
		$id_field = new SwatDBField($id_field, 'integer');
		$title_field = new SwatDBField($title_field, 'text');
		$level_field = new SwatDBField($level_field, 'integer');
		
		$types = array($id_field->type, $title_field->type, $level_field->type);
		
		$rs = $db->executeStoredProc($sp, array(0), $types, true);
		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		$tree = SwatDB::buildTreeOptionArray($rs, $title_field->name, $id_field->name, $level_field->name);
		return $tree;
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
	 */
	public static function getFieldMax($db, $table, $field)
	{
		$field = new SwatDBField($field, 'integer');
			
		$sql = sprintf('select max(%s) as %s from %s',
			$field->name, $field->name, $table);

		SwatDB::debug($sql);
		$rs = $db->query($sql, array($field->type));
		
		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());
		
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);
		$field_name = $field->name;
		return $row->$field_name;
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
		if ($value === null && $neg)
			return 'is not';
		elseif ($value === null)
			return 'is';
		elseif ($neg)
			return '!=';
		else
			return '=';
	}

	// }}}
	// {{{ private static function buildTreeOptionArray)

	private static function buildTreeOptionArray($rs, $title_field_name,
		$id_field_name, $level_field_name)
	{
		$stack = array();
		$current_parent =  new SwatTreeNode();
		$base_parent = $current_parent;
		array_push($stack, $current_parent);
		$last_node = $current_parent;	

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$title = $row->$title_field_name;
			$id = $row->$id_field_name;
			$level = $row->$level_field_name;
			
			if ($level > count($stack)) {
				array_push($stack, $current_parent);
				$current_parent = $last_node;
			} else if ($level < count($stack)) {
				$current_parent = array_pop($stack);
			}
		
			$last_node = new SwatTreeNode(array('title' => $title, 'value' => $id));
			$current_parent->children[] = $last_node;
		}

		return $base_parent;
	}

	// }}}
	// {{{ private static function getFieldNameArray()

	private static function getFieldNameArray($fields)
	{
		if (count($fields) == 0)
			return;

		$names = array();

		foreach ($fields as &$field)
			$names[] = $field->name;

		return $names;
	}

	// }}}
	// {{{ private static function getFieldTypeArray()

	private static function getFieldTypeArray($fields)
	{
		if (count($fields) == 0)
			return;

		$types = array();

		foreach ($fields as &$field)
			$types[] = $field->type;

		return $types;
	}

	// }}}
	// {{{ private static function initFields()

	private function initFields(&$fields)
	{
		/* Transforms and array of text field identifiers ('text:title') into
		 * an array of SwatDBField objects.
		 */
		if (count($fields) == 0)
			// TODO: throw exception instead of returning
			return;

		foreach ($fields as &$field)
			$field = new SwatDBField($field, 'text');
	}

	// }}}
	// {{{ private static function debug()

	private static function debug($msg)
	{
		if (defined('SWATDB_DEBUG')) {
			echo $msg, '<hr />';
		}
	}

	// }}}
}

?>
