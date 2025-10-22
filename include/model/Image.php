<?php
declare(strict_types=1);

require_once 'const.php';
require_once 'util.php';
require_once 'model/Model.php';

final class Image extends Model
{
    protected static function key_fields() { return ['id' => [null, 'id', PDO::PARAM_INT]]; }

    protected static function fields() { return ['mime_subtype' => [null, 'mime_subtype', PDO::PARAM_STR]]; }

    function __construct(
        protected ?int $id,
        public string $mime_subtype,
        private ?string $tmp_name = null
    ) {}

    static function from_db(int $id_image): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select id, mime_subtype from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_image, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new self($row['id'], $row['mime_subtype']);
    }

    function move_uploaded_image(): void
    {
        if ($this->tmp_name === null) return;
        $dest = DB\document_root() . $this->upload_location();
        $dir = dirname($dest);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        error_log("Moving uploaded image '$this->tmp_name' to '$dest'.");
        notfalse(move_uploaded_file($this->tmp_name, $dest));
        $this->tmp_name = null;
    }

    function src(): string
    {
        if ($this->tmp_name !== null) return notfalse(self::image_data_uri($this->tmp_name));
        if ($this->id === null || empty($this->mime_subtype))
            throw new LogicException('Image non initialisée correctement.');
        return $this->upload_location();
    }

    private function upload_location(): string
    {
        return "/images_utilisateur/$this->id.$this->mime_subtype";
    }

    /**
    * Retourne la représentation data-uri du fichier image spécifié.
    * @param string $path Chemin du fichier
    * @param bool $forceBase64 Toujours utiliser Base64 (utile pour les SVG)
    * @return string|false La chaîne data-uri, ou false en cas d'erreur
    */
    protected static function image_data_uri(string $path, bool $forceBase64 = false): string|false {
        if (!$path || !is_readable($path)) {
            return false;
        }

        $data = file_get_contents($path);
        if ($data === false) {
            return false;
        }

        // Détermine le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_buffer($finfo, $data) : null;
        finfo_close($finfo);
        if (!$mime) {
            return false;
        }

        // Normalisation du type MIME
        if (in_array($mime, ['image/svg', 'text/xml'], true)) {
            $mime = 'image/svg+xml';
        }

        // Traitement spécial pour les SVG
        if ($mime === 'image/svg+xml') {
            $data = preg_replace('/^\xEF\xBB\xBF/', '', $data); // Retire BOM UTF-8
            $data = preg_replace('/^.*?<svg/s', '<svg', $data); // Coupe avant <svg
            if (strpos($data, 'xmlns=') === false) {
                $data = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $data);
            }

            if (!$forceBase64) {
                // Version texte encodée
                $data = preg_replace('/\s+/', ' ', trim($data));
                $data = preg_replace('/"/', "'", $data);
                $encoded = rawurlencode($data);
                $encoded = str_replace(['%20', '%27', '%2C', '%3D', '%3A', '%2F'], [' ', "'", ',', '=', ':', '/'], $encoded);
                return "data:$mime,$encoded";
            }
        }

        // Fallback : encodage Base64
        return "data:$mime;base64," . base64_encode($data);
    }

    const TABLE = '_image';
}