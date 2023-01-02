<?php

namespace Quods\Whatsapp\Models;

use Quods\Whatsapp\Traits\OwnUserTable;
use Quods\Whatsapp\Whatsapp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, OwnUserTable;

    protected static $table_master = 'contacts';

    protected $guarded = ['id', 'created_at'];

    public function scopeKategori(string $kategori)
    {
        return $this->where('kategori', $kategori);
    }

    public function addTag(array $tag)
    {
        $tags = explode(', ', $this->tag);
        $this->tag = implode(', ', array_unique(array_merge($tags, $tag)));
        $this->save();
    }

    public function removeTag(array $tag)
    {
        $tags = explode(', ', $this->tag);
        $this->tag = implode(', ', array_diff($tags, $tag));
        $this->save();
    }

    /**
     * @param $device Bisa id atau langsung Model Device
     * @param $text Pesan yang akan dikirim
     * 
     * @return Whatsapp 
     */
    public function sendMessage($device, $text)
    {
        return Whatsapp::device($device)->data($this->attributes)->copywriting($text);
    }
}
