<?php

namespace HazreTours\Tasks;

/**
 * Čisti program putovanja prenesen sa starog sajta:
 * - sav tekst je tamo bio podebljan i u kurzivu, pa se <strong>/<b>/<em>/<i> uklanjaju;
 * - tabela "Aranžman obuhvata / ne obuhvata" se vadi u posebne liste (na sajtu su u kartici Opis);
 * - prazni pasusi i razmaci (&nbsp;) se brišu.
 */
class ProgramCleaner
{
    /**
     * @return array{html: string, includes: string, excludes: string}
     */
    public static function clean(string $html): array
    {
        $includes = [];
        $excludes = [];

        // Tabela s uslugama: lista nakon "obuhvata" ide u uključeno, nakon "ne obuhvata" u neuključeno
        $html = preg_replace_callback('/<table\b.*?<\/table>/is', function ($m) use (&$includes, &$excludes) {
            $table = $m[0];
            if (!preg_match('/obuh/iu', strip_tags($table))) {
                return $table;
            }
            $parts = preg_split('/(ne\s+obuhvata)/iu', $table, 2);
            $includes = self::items($parts[0]);
            $excludes = isset($parts[1]) ? self::items($parts[1]) : [];
            return '';
        }, $html);

        $html = preg_replace('/<\/?(strong|b|em|i|u|span)\b[^>]*>/i', '', $html);
        $html = str_replace(["\u{00A0}", '&nbsp;'], ' ', $html);
        $html = preg_replace('/[ \t]{2,}/', ' ', $html);
        $html = preg_replace('/<(p|li|h[2-4])>\s*<\/\1>/i', '', $html);
        $html = preg_replace('/<(ul|ol)>\s*<\/\1>/i', '', $html);
        $html = preg_replace('/\n{2,}/', "\n", $html);

        return [
            'html' => trim($html),
            'includes' => implode("\n", $includes),
            'excludes' => implode("\n", $excludes),
        ];
    }

    /** Stavke liste (<li>) kao čist tekst, prvo slovo veliko. */
    private static function items(string $html): array
    {
        preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $html, $m);
        $out = [];
        foreach ($m[1] as $li) {
            $t = trim(preg_replace('/\s+/u', ' ', str_replace(["\u{00A0}", '&nbsp;'], ' ', html_entity_decode(strip_tags($li), ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
            if ($t !== '') {
                $out[] = mb_strtoupper(mb_substr($t, 0, 1)) . mb_substr($t, 1);
            }
        }
        return $out;
    }
}
