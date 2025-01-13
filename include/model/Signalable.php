<?php
require_once 'db.php';

// not abstract so we don't have to figure out which concrete class an id belongs, we don't need to anyway.
class Signalable extends Model {
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected function __construct(
        protected ?int $id
    ) {}

    static function from_db(int $id_signalable) {
        return new self($id_signalable);
    }

    function signaler(int $id_compte, string $raison) {
        $stmt = DB\connect()->prepare('insert into _signalement (id_signalable,id_compte,raison) values (?,?,?)');
        DB\bind_values($stmt, [1 => $this->id, 2 => $id_compte, 3 => $raison]);
        notfalse($stmt->execute());
    }
}
