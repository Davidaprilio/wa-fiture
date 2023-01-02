<?php

namespace Quods\Whatsapp\Models;

use Carbon\Carbon;
use Quods\Whatsapp\Traits\OwnUserTable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Message extends Model
{
    use HasFactory, SoftDeletes, OwnUserTable;

    protected $guarded = ['id', 'created_at'];

    protected static $table_master = 'messages';

    protected $casts = [
        'payload' => AsArrayObject::class,
    ];

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
