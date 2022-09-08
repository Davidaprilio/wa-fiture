<?php

namespace DavidArl\WaFiture\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at'];

    protected static $table_master = 'messages';

    protected static $table_user = 'zu*_messages';

    protected $casts = [
        'payload' => AsArrayObject::class,
    ];

    public function __construct($user = null, $auto_create_table = false)
    {
        parent::__construct();

        if ($user === null) {
            $table_name = self::$table_master;
        } else {
            if ($user instanceof User) {
                $user_id = $user->id;
            } elseif (is_numeric($user)) {
                $user_id = $user;
            } else {
                throw new \Exception('User id untuk table message Invalid');
            }

            if ($auto_create_table) {
                $table_name = $this->init($user_id);
            } else {
                $table_name = $this->getTableUser($user_id);
            }
        }
        $this->table = $table_name;
        $this->setConnection('mysql');
    }

    public static function zu($user_id = null, $auto_create_table = false): self
    {
        if ($user_id === null) {
            if (Auth::guest()) {
                throw new \Exception('User Belum Login, tidak bisa menggunakan method zu() jika user belum login');
            }
            $user_id = Auth::id();
        }

        return new self($user_id, $auto_create_table);
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
        if (! $cektable) {
            DB::statement("create table {$tb} like ".self::$table_master);
        }

        return $tb;
    }

    private static function getTableUser(int $user_id): string
    {
        return str_replace('*', $user_id, self::$table_user);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopePreview($query)
    {
        return $query->where('status', 0);
    }

    public function scopeProses($query, $proses_code)
    {
        return $query->where('proses_code', $proses_code);
    }

    public function scopePesanKenaLimit($query)
    {
        return $query->whereIn('status', [2, 3])
            ->where('report', '<>', 'Whastapp tidak merespon')
            ->whereDate('created_at', \Carbon\Carbon::now())
            ->withTrashed();
    }

    public function scopeLast($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }
}
