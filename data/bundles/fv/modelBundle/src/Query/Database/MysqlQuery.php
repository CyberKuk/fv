<?php

namespace Bundle\fv\ModelBundle\Query\Database;

class MysqlQuery extends DatabaseQuery {

    public function insert() {
        $this->prepareAndExecute( "INSERT INTO {$this->getTableName()} {$this->getValuesClause()}" );
        return $this->getDriver()->lastInsertId();
    }

    public function delete() {
        $this->prepareAndExecute( "DELETE FROM {$this->getTableName()} {$this->getWhereClause()} {$this->getLimitClause()}" );
        return true;
    }

    public function update() {
        // @TODO: Implement update() method.
    }

    protected function extract() {
        return
            $this
                ->prepareAndExecute( "SELECT * FROM {$this->getTableName()} {$this->getWhereClause()} {$this->getLimitClause()}" )
                ->fetchAll();
    }

    /**
     * @return \fv\Connection\Driver\PdoMysqlDriver
     */
    private function getDriver(){
        return $this->getConnection()->getDriver();
    }

    /**
     * @param string $sql
     *
     * @return \PDOStatement
     */
    private function prepareAndExecute( $sql ){
        /** @var $sth \PDOStatement */
        $sth = $this->getDriver()->prepare( $sql );
        $sth->setFetchMode( \PDO::FETCH_ASSOC );
        $sth->execute( $this->preparedParams() );
        return $sth;
    }

    private function getWhereClause(){
        $where = array();
        foreach( $this->getWhere() as $statement ){
            $where[] = "({$statement})";
        }

        if( empty($where) )
            return "";

        $where = "WHERE " . implode(" AND ", $where);

        return $where;
    }

    private function getValuesClause(){
        $params = "";
        $keys = "";
        foreach( $this->getSetKeys() as $key ){
            $keys .= "{$key},";
            $params .= ":{$key},";
        }
        $params = rtrim($params, ",");
        $keys = rtrim($keys, ",");

        return "({$keys}) VALUES ($params)";
    }

    private function getLimitClause(){
        if( ! $this->getLimitCount() && ! $this->getLimitOffset() )
            return "";

        if( ! $this->getLimitOffset() ){
            return "LIMIT " . $this->getLimitCount();
        }

        if( ! $this->getLimitCount() ){
            throw new \Bundle\fv\ModelBundle\Exception\QueryException("Can't perform limit offset without limit count given");
        }

        return "LIMIT {$this->getLimitCount()} OFFSET {$this->getLimitOffset()}";
    }

    private function preparedParams(){
        return array_merge( $this->getWhereParams(), $this->getHavingParams(), $this->getSetParams() );
    }

    public function getTableName() {
        return '`' . parent::getTableName() . '`';
    }


}
