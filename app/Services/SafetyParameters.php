<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafetyParameters
{
    /** @var array<string, mixed>|null */
    private ?array $parameters = null;

    /** @return array<string, mixed> */
    public function all(): array
    {
        if ($this->parameters !== null) {
            return $this->parameters;
        }

        $configured = config('safety', []);
        $configured = is_array($configured) ? $configured : [];

        if (! Schema::hasTable('security_parameters')) {
            return $this->parameters = $configured;
        }

        $columns = Schema::getColumnListing('security_parameters');

        $keyColumn = in_array('parameter_key', $columns, true)
            ? 'parameter_key'
            : (in_array('key', $columns, true) ? 'key' : null);
        $valueColumn = in_array('parameter_value', $columns, true)
            ? 'parameter_value'
            : (in_array('value', $columns, true) ? 'value' : null);

        if ($keyColumn !== null && $valueColumn !== null) {
            $databaseParameters = [];

            foreach (DB::table('security_parameters')->get([$keyColumn, $valueColumn]) as $row) {
                if (is_string($row->{$keyColumn}) && $row->{$valueColumn} !== null) {
                    $databaseParameters[$row->{$keyColumn}] = $this->decodeValue($row->{$valueColumn});
                }
            }

            return $this->parameters = array_replace_recursive($configured, $databaseParameters);
        }

        $row = DB::table('security_parameters')->first();

        if ($row === null) {
            return $this->parameters = $configured;
        }

        $databaseParameters = [];

        foreach ((array) $row as $key => $value) {
            if ($key === 'id' || $key === 'created_at' || $key === 'updated_at' || $value === null) {
                continue;
            }

            $decoded = $this->decodeValue($value);
            $databaseParameters[$key] = $decoded;
        }

        return $this->parameters = array_replace_recursive($configured, $databaseParameters);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    private function decodeValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}
