<?php
require_once 'const.php';
require_once 'util.php';
require_once 'model/Model.php';

/**
 * @property-read ?int $id L'ID. `null` si cette image n'existe pas dans la BDD.
 * @property int $taille
 * @property string $mime_subtype
 * @property string $legende
 */
final class Image extends Model
{
    protected const FIELDS = [
        'taille'       => [[null, 'taille',       PDO::PARAM_INT]],
        'mime_subtype' => [[null, 'mime_subtype', PDO::PARAM_STR]],
        'legende'      => [[null, 'legende',      PDO::PARAM_STR]],
    ];

    protected static function key_fields()
    {
        return [
            'id' => ['id', PDO::PARAM_INT, null],
        ];
    }

    protected ?int $id;
    protected int $taille;
    protected string $mime_subtype;
    protected ?string $legende;
    protected ?string $tmp_name;

    function __construct(
        ?int $id,
        int $taille,
        string $mime_subtype,
        ?string $legende,
        ?string $tmp_name = null,
    ) {
        $this->taille       = $taille;
        $this->mime_subtype = $mime_subtype;
        $this->legende      = $legende;
        $this->id           = $id;
        $this->tmp_name     = $tmp_name;
    }

    static function from_db(int $id_image): Image|false
    {
        $stmt = notfalse(DB\connect()->prepare('select taille, mime_subtype, legende from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_image, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new Image(
            $id_image,
            $row['taille'],
            $row['mime_subtype'],
            $row['legende'],
        );
    }

    /**
     * Déplace cette image téléversée vers le dossier des images utilisateur.
     * @throws LogicException
     * @return void
     */
    function move_uploaded_image(): void
    {
        if ($this->tmp_name === null) {
            throw new LogicException("Impossible de déplacer l'image. Soit l'image a déjà été déplacée, soit elle provient de la BDD");
        }
        notfalse(move_uploaded_file($this->tmp_name, DOCUMENT_ROOT . $this->upload_location()));
        $this->tmp_name = null;
    }

    function display_location(): string
    {
        return $this->tmp_name === null
            ? $this->upload_location()
            : notfalse(self::image_data_uri($this->tmp_name));
    }

    private function upload_location(): string
    {
        return "/images_utilisateur/$this->id.$this->mime_subtype";
    }

    const TABLE = '_image';

    /**
     * Retourne la représentation data-uri du fichier image spécifié.
     * @param string $path Chemin du fichier
     * @param bool $forceBase64 Toujours utiliser la forme Base64, utilisé uniquement pour les SVGs
     * @return string|false La chaine data-uri, ou false en cas d'erreur
     */
    protected static function image_data_uri(string $path, bool $forceBase64 = false): string|false
    {
        // Vérifie si le fichier est lisible
        if (!$path || !@is_readable($path)) return false;

        // Lit le contenu du fichier
        $data = file_get_contents($path);
        if ($data === false) return false;

        // Supprime le marqueur utf8-bom du contenu s'il est présent
        if ("\u{FEFF}" == substr($data, 0, 3)) $data = substr($data, 3);

        // Détermine le type MIME du contenu
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) return false;
        $mime = finfo_buffer($finfo, $data);
        finfo_close($finfo);
        if (!$mime) return false;

        // Correction du type MIME dans certains cas
        if ($mime == 'image/svg') $mime = 'image/svg+xml';
        if ($mime == 'text/xml') $mime  = 'image/svg+xml';

        // Correction du code SVG si nécessaire
        if ($mime == 'image/svg+xml') {
            if ('<svg' != substr($data, 0, 4)) $data                         = substr($data, strpos($data, '<svg'));
            if (strpos($data, 'http://www.w3.org/2000/svg') === false) $data = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $data);
        }

        // Génération data-uri en texte URL
        if ($mime == 'image/svg+xml' && !$forceBase64) {
            $data = trim($data);
            $data = preg_replace('/\s+/', ' ', $data);
            $data = preg_replace('/"/', "'", $data);
            $data = rawurlencode($data);
            $data = str_replace(['%20', '%27', '%2C', '%3D', '%3A', '%2F'], [' ', "'", ',', '=', ':', '/'], $data);

            $result  = "data:$mime,";
            $result .= $data;
            return $result;
        }

        // Génération data-uri en Base64
        $result  = "data:$mime;base64,";
        $result .= base64_encode($data);
        return $result;
    }
}
