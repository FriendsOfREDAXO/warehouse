<?php

namespace FriendsOfRedaxo\Warehouse;

use PDOException;
use rex_fragment;
use rex_sql_exception;

class Search extends \rex_sql
{
    /**
     *
     * @param string $query
     * @param int $limit
     * @return array<list, array<string, bool|float|int|string|null>>
     * @throws rex_sql_exception
     * @throws PDOException
     */
    public static function query(string $query, int $limit = 50) :array
    {
        $sql = self::factory();
        // Es ist möglich, jedem inneren SELECT ein eigenes LIMIT zu geben.
        // Das beeinflusst, wie viele Zeilen pro Teilabfrage maximal zurückgegeben werden.
        // Beispiel: LIMIT 10 pro SELECT

        $sql_query = '
        SELECT * FROM (
            SELECT
            \'article\' AS source,
            id,
            uuid,
            name,
            short_text AS details,
            createdate,
            updatedate
            FROM rex_warehouse_article
            WHERE 
                MATCH(`name`, `short_text`, `uuid`) AGAINST (:query  IN BOOLEAN MODE)
                OR uuid LIKE CONCAT(\'%\', :query, \'%\')
                OR id = :query
    
            UNION

            SELECT
            \'article_variant\' AS source,
            id,
            uuid,
            name,
            NULL AS details,
            createdate,
            updatedate
            FROM rex_warehouse_article_variant
            WHERE 
                MATCH(`name`, `uuid`) AGAINST (:query  IN BOOLEAN MODE)
                OR uuid LIKE CONCAT(\'%\', :query, \'%\')
                OR id = :query
    
            UNION

            SELECT
            \'order\' AS source,
            id AS id,
            \'\' as uuid,
            CONCAT(firstname, \' \', lastname) AS name,
            CONCAT(company, \', \', email) AS details,
            createdate,
            updatedate
            FROM rex_warehouse_order
            WHERE 
                MATCH(`firstname`, `lastname`, `company`, `email`) AGAINST (:query  IN BOOLEAN MODE)
                OR id = :query
            
            UNION

            SELECT
            \'category\' AS source,
            id,
            uuid,
            name,
            NULL AS details,
            createdate,
            updatedate

            FROM rex_warehouse_category
            WHERE 
                MATCH(`name`, `uuid`) AGAINST (:query IN BOOLEAN MODE)
                OR uuid LIKE CONCAT(\'%\', :query, \'%\')
                OR id = :query
    
        ) AS results
        ORDER BY updatedate DESC
        LIMIT :limit';

        return $sql->getArray($sql_query, ['query' => trim($query), 'limit' => $limit]);

    }

    public static function getForm() : string
    {
        $fragment = new rex_fragment();
        return $fragment->parse('warehouse/backend/search.php');
    }

}
