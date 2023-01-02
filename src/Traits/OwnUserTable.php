<?php

namespace Quods\Whatsapp\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 
 */
trait OwnUserTable
{
    public function __construct($user = null, $auto_create_table = false)
    {
        parent::__construct();
        $this->generateOwnTable($user, $auto_create_table);
    }

    public function generateOwnTable($user = null, $auto_create_table = false)
    {
        if ($user === null) {
            $this->table = self::$table_master;
        } else {
            $user_id = $this->validationUserID($user);

            if ($auto_create_table) {
                $this->table = $this->init($user_id);
            } else {
                $this->table = $this->getTableUser($user_id);
            }
        }
        $this->setConnection('mysql');
    }

    public function validationUserID($user): int
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } elseif (is_numeric($user)) {
            $user_id = $user;
        } else {
            throw new \Exception("User id untuk table {$this->table_master} Invalid");
        }
        return (int) $user_id;
    }

    public static function zu($user_id = null, $auto_create_table = false): self
    {
        if ($user_id === null) {
            return self::zauth($auto_create_table);
        }

        return new self($user_id, $auto_create_table);
    }

    public static function zauth($auto_create_table = false): self
    {
        if (Auth::guest()) {
            throw new \Exception('User Belum Login, tidak bisa menggunakan method zauth() jika user belum login');
        }

        return new self(Auth::id(), $auto_create_table);
    }

    /**
     * Create User table if not found
     *
     * @param  int|string  $id User_ID default curent user loged
     * @return string Table Name
     */
    public static function init($id = false): string
    {
        $id = (int) ($id ?? Auth::id());
        if (gettype($id) !== 'integer') {
            throw new \Exception('User ID must be integer');
        }
        $tb = self::getTableUser($id);
        $cektable = Schema::hasTable($tb);
        if (!$cektable) {
            DB::statement("create table {$tb} like " . self::$table_master);
        }

        return $tb;
    }

    private static function getTableUser(int $user_id): string
    {
        return "zu{$user_id}_" . self::$table_master;
    }
}
