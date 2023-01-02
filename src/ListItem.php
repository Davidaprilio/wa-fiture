<?php 

namespace Quods\Whatsapp;

class ListItem {
    protected $rows = [];

    public function add(string $title, string $description, $id = null)
    {
        $this->rows[] = [
            'title' => $title,
            'description' => $description,
            'rowId' => $id ?? str()->random(5),
        ];
        return $this;
    }

    public function getRows()
    {
        return $this->rows;
    }
}