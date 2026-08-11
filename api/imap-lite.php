<?php
// Fichier: abys-ai/api/imap-lite.php
// Client IMAP minimal en PHP pur (socket TLS 993) : AUCUNE dépendance à ext-imap.
// Couvre le besoin de Milo : lister les non-lus, lire un message, marquer lu.
// Même philosophie que le client SMTP pur de email.php.

class ImapLiteException extends Exception {}

class ImapLite {
    private $fp;
    private int $tagN = 0;

    public function __construct(string $host, int $port, string $user, string $pass, int $timeout = 15) {
        $this->fp = @stream_socket_client(
            "ssl://{$host}:{$port}", $errno, $errstr, $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]])
        );
        if (!$this->fp) throw new ImapLiteException("Connexion IMAP échouée : {$errstr}");
        stream_set_timeout($this->fp, $timeout);
        $greet = fgets($this->fp, 2048);
        if (strpos((string)$greet, '* OK') !== 0) throw new ImapLiteException('Greeting IMAP inattendu : ' . trim((string)$greet));
        $this->cmd('LOGIN ' . $this->quote($user) . ' ' . $this->quote($pass));
    }

    private function quote(string $s): string {
        return '"' . addcslashes($s, "\\\"") . '"';
    }

    /**
     * Envoie une commande taguée et lit jusqu'à la réponse taguée.
     * Gère les littéraux {n} en lisant exactement n octets.
     * Retourne toutes les lignes non taguées (réponses *) + la ligne finale.
     */
    public function cmd(string $command): array {
        $tag = 'A' . (++$this->tagN);
        fwrite($this->fp, $tag . ' ' . $command . "\r\n");
        $lines = [];
        $guard = 0;
        while (true) {
            if (++$guard > 10000) throw new ImapLiteException('Réponse IMAP trop longue');
            $line = fgets($this->fp, 8192);
            if ($line === false) throw new ImapLiteException('Connexion IMAP interrompue');

            // Littéral annoncé en fin de ligne : {123}\r\n suivi de 123 octets bruts
            while (preg_match('/\{(\d+)\}\r?\n$/', $line, $m)) {
                $need = (int)$m[1];
                $data = '';
                while (strlen($data) < $need) {
                    $chunk = fread($this->fp, min(8192, $need - strlen($data)));
                    if ($chunk === false || $chunk === '') break 2;
                    $data .= $chunk;
                }
                $line .= $data . (fgets($this->fp, 8192) ?: '');
            }

            if (strpos($line, $tag . ' ') === 0) {
                if (strpos($line, $tag . ' OK') !== 0) {
                    throw new ImapLiteException('IMAP ' . trim(substr($line, strlen($tag) + 1)));
                }
                $lines[] = $line;
                return $lines;
            }
            $lines[] = $line;
        }
    }

    public function selectInbox(): void { $this->cmd('SELECT INBOX'); }

    /** @return int[] numéros de séquence des messages non lus */
    public function searchUnseen(): array {
        foreach ($this->cmd('SEARCH UNSEEN') as $l) {
            if (preg_match('/^\*\s+SEARCH\s*(.*)$/i', trim($l), $m)) {
                $ids = array_filter(array_map('intval', preg_split('/\s+/', trim($m[1]))));
                return array_values($ids);
            }
        }
        return [];
    }

    /** Message brut complet (RFC822), sans le marquer lu. Tronqué à $maxBytes. */
    public function fetchRaw(int $num, int $maxBytes = 262144): string {
        $lines = $this->cmd("FETCH {$num} (BODY.PEEK[])");
        $raw = implode('', $lines);
        // Extraire le littéral : tout ce qui suit la 1re annonce {n}
        if (preg_match('/\{(\d+)\}\r?\n/', $raw, $m, PREG_OFFSET_CAPTURE)) {
            $start = $m[0][1] + strlen($m[0][0]);
            $len   = min((int)$m[1][0], $maxBytes);
            return substr($raw, $start, $len);
        }
        return '';
    }

    public function markSeen(int $num): void {
        $this->cmd("STORE {$num} +FLAGS (\\Seen)");
    }

    public function close(): void {
        try { $this->cmd('LOGOUT'); } catch (Exception $e) {}
        if (is_resource($this->fp)) fclose($this->fp);
    }
}

// ── Parseur MIME minimal : en-têtes + corps texte ─────────────────────────────

function imaplite_decode_header(string $s): string {
    if (function_exists('iconv_mime_decode')) {
        $d = @iconv_mime_decode($s, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        if ($d !== false) return trim($d);
    }
    return trim($s);
}

function imaplite_decode_body(string $body, string $encoding, string $charset): string {
    $encoding = strtolower(trim($encoding));
    if ($encoding === 'base64')            $body = base64_decode($body) ?: $body;
    elseif ($encoding === 'quoted-printable') $body = quoted_printable_decode($body);
    $charset = strtoupper(trim($charset, " \t\"'"));
    if ($charset && $charset !== 'UTF-8' && function_exists('mb_convert_encoding')) {
        $c = @mb_convert_encoding($body, 'UTF-8', $charset);
        if ($c !== false && $c !== null) $body = $c;
    }
    return $body;
}

/**
 * Parse un message brut : from_email, from_name, subject, message_id, body (texte).
 * Gère le multipart simple (préfère text/plain, sinon text/html débalisé).
 */
function imaplite_parse(string $raw): array {
    $raw = str_replace("\r\n", "\n", $raw);
    [$head, $body] = array_pad(explode("\n\n", $raw, 2), 2, '');

    // Déplier les en-têtes
    $head = preg_replace('/\n[ \t]+/', ' ', $head);
    $H = [];
    foreach (explode("\n", $head) as $l) {
        if (preg_match('/^([A-Za-z0-9\-]+):\s*(.*)$/', $l, $m)) $H[strtolower($m[1])] = $m[2];
    }

    $fromRaw = imaplite_decode_header($H['from'] ?? '');
    $from_email = ''; $from_name = '';
    if (preg_match('/<([^>]+)>/', $fromRaw, $m)) {
        $from_email = strtolower(trim($m[1]));
        $from_name  = trim(str_replace(['"', "'"], '', preg_replace('/<[^>]+>/', '', $fromRaw)));
    } elseif (filter_var(trim($fromRaw), FILTER_VALIDATE_EMAIL)) {
        $from_email = strtolower(trim($fromRaw));
    }
    if (!$from_name) $from_name = $from_email;

    $subject    = imaplite_decode_header($H['subject'] ?? '(sans objet)');
    $message_id = trim($H['message-id'] ?? '') ?: uniqid('abys_', true);
    $ctype      = $H['content-type'] ?? 'text/plain';
    $cte        = $H['content-transfer-encoding'] ?? '7bit';

    $text = '';
    if (preg_match('/multipart\/[a-z]+;.*boundary="?([^";]+)"?/is', $ctype, $m)) {
        $parts = explode('--' . trim($m[1]), $body);
        $plain = ''; $html = '';
        foreach ($parts as $part) {
            [$ph, $pb] = array_pad(explode("\n\n", ltrim($part, "\n"), 2), 2, '');
            $ph = strtolower(preg_replace('/\n[ \t]+/', ' ', $ph));
            $penc = 'utf-8'; $pcte = '7bit';
            if (preg_match('/charset=([^\s;]+)/', $ph, $mm)) $penc = $mm[1];
            if (preg_match('/content-transfer-encoding:\s*(\S+)/', $ph, $mm)) $pcte = $mm[1];
            if (strpos($ph, 'text/plain') !== false && !$plain) $plain = imaplite_decode_body($pb, $pcte, $penc);
            elseif (strpos($ph, 'text/html') !== false && !$html) $html = imaplite_decode_body($pb, $pcte, $penc);
        }
        $text = $plain ?: strip_tags(preg_replace('/<br\s*\/?>|<\/p>/i', "\n", $html));
    } else {
        $charset = 'utf-8';
        if (preg_match('/charset=([^\s;]+)/i', $ctype, $m)) $charset = $m[1];
        $text = imaplite_decode_body($body, $cte, $charset);
        if (stripos($ctype, 'text/html') !== false) {
            $text = strip_tags(preg_replace('/<br\s*\/?>|<\/p>/i', "\n", $text));
        }
    }

    return [
        'from_email' => $from_email,
        'from_name'  => $from_name,
        'subject'    => $subject,
        'message_id' => $message_id,
        'body'       => trim($text),
    ];
}
