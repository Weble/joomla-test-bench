<?php

namespace Weble\JoomlaTestBench\Concerns;

class DbTestHelper
{

    /**
     * @param string $query Query
     *
     * @return array
     *
     * @since   4.0.0
     */
    public static function splitQueries($query)
    {
        $buffer    = [];
        $queries   = [];
        $in_string = false;

        // Trim any whitespace.
        $query = trim($query);

        // Remove comment lines.
        $query = preg_replace("/\n\#[^\n]*/", '', "\n" . $query);

        // Remove PostgreSQL comment lines.
        $query = preg_replace("/\n\--[^\n]*/", '', "\n" . $query);

        // Find function.
        $funct = explode('CREATE OR REPLACE FUNCTION', $query);

        // Save sql before function and parse it.
        $query = $funct[0];

        // Parse the schema file to break up queries.
        for ($i = 0; $i < strlen($query) - 1; $i++) {
            if ($query[$i] == ';' && ! $in_string) {
                $queries[] = substr($query, 0, $i);
                $query     = substr($query, $i + 1);
                $i         = 0;
            }

            if ($in_string && ($query[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            } elseif (! $in_string && ($query[$i] == '"' || $query[$i] == "'") && (! isset($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $query[$i];
            }

            if (isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }

            $buffer[1] = $query[$i];
        }

        // If the is anything left over, add it to the queries.
        if (! empty($query)) {
            $queries[] = $query;
        }

        // Add function part as is.
        for ($f = 1, $fMax = count($funct); $f < $fMax; $f++) {
            $queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
        }

        return $queries;
    }
}
