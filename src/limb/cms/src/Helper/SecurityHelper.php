<?php

namespace limb\cms\src\Helper;

class SecurityHelper
{
    static function guidv4($data)
    {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    static function guidv4_rand()
    {
        return self::guidv4(openssl_random_pseudo_bytes(16));
    }

    static function gen_salt()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    static function generatePassword()
    {
        $alphabet = array(
            array('b', 'c', 'd', 'f', 'g', 'h', 'g', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z',
                'B', 'C', 'D', 'F', 'G', 'H', 'G', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Z'),
            array('a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y'),
        );

        $new_password = '';
        for ($i = 0; $i < 9; $i++) {
            $j = $i % 2;
            $min_value = 0;
            $max_value = count($alphabet[$j]) - 1;
            $key = rand($min_value, $max_value);
            $new_password .= $alphabet[$j][$key];
        }
        return $new_password;
    }

    /** @param string $password User password */
    /** @param string $salt salt */
    /** @return string Hashed password */
    static function cryptPassword($password, $salt): string
    {
        $hash = hash_pbkdf2("sha256", $password, $salt, $iterations = 200000, $length = 32);
        return $hash;
    }

    /* */
    static function DwarfNames()
    {
        $syllable_1 = array("B", "D", "F", "G", "Gl", "H", "K", "L", "M", "N", "R", "S", "T", "Th", "V");
        $syllable_2 = array("a", "e", "i", "o", "oi", "u");
        $syllable_3 = array("bur", "fur", "gan", "gnus", "gnar", "li", "lin", "lir", "mli", "nar", "nus", "rin", "ran", "sin", "sil", "sur");

        return ($syllable_1[rand(0, 14)] . $syllable_2[rand(0, 5)] . $syllable_3[rand(0, 15)]);
    }

    static function ElfNames()
    {
        $syllable_1 = array("Al", "An", "Bal", "Bel", "Cal", "Cel", "El", "Ell", "Elr", "Elv", "Eow", "Eдr", "F", "Fal", "Fel", "Fin", "G", "Gal", "Gel", "Gl", "Is", "Lan", "Leg", "Lуm", "N", "Nal", "Nel", "S", "Sal", "Sel", "T", "Tal", "Tel", "Thr", "Tin");
        $syllable_2 = array("a", "б", "adrie", "ara", "e", "й", "ebri", "ele", "ere", "i", "io", "ithra", "ilma", "il-Ga", "ili", "o", "orfi", "у", "u", "y");
        $syllable_3 = array("l", "las", "lad", "ldor", "ldur", "lindл", "lith", "mir", "n", "nd", "ndel", "ndil", "ndir", "nduil", "ng", "mbor", "r", "rith", "ril", "riand", "rion", "s", "ssar", "thien", "viel", "wen", "wyn");

        return ($syllable_1[rand(0, 34)] . $syllable_2[rand(0, 19)] . $syllable_3[rand(0, 26)]);
    }

    static function GnomeNames()
    {
        $syllable_1 = array("Aar", "An", "Ar", "As", "C", "H", "Han", "Har", "Hel", "Iir", "J", "Jan", "Jar", "K", "L", "M", "Mar", "N", "Nik", "Os", "Ol", "P", "R", "S", "Sam", "San", "T", "Ter", "Tom", "Ul", "V", "W", "Y");
        $syllable_2 = array("a", "aa", "ai", "e", "ei", "i", "o", "uo", "u", "uu");
        $syllable_3 = array("ron", "re", "la", "ki", "kseli", "ksi", "ku", "ja", "ta", "na", "namari", "neli", "nika", "nikki", "nu", "nukka", "ka", "ko", "li", "kki", "rik", "po", "to", "pekka", "rjaana", "rjatta", "rjukka", "la", "lla", "lli", "mo", "nni");

        return ($syllable_1[rand(0, 32)] . $syllable_2[rand(0, 9)] . $syllable_3[rand(0, 31)]);
    }

    static function HalflingNames()
    {
        $syllable_1 = array("B", "Ber", "Br", "D", "Der", "Dr", "F", "Fr", "G", "H", "L", "Ler", "M", "Mer", "N", "P", "Pr", "Per", "R", "S", "T", "W");
        $syllable_2 = array("a", "e", "i", "ia", "o", "oi", "u");
        $syllable_3 = array("bo", "ck", "decan", "degar", "do", "doc", "go", "grin", "lba", "lbo", "lda", "ldo", "lla", "ll", "lo", "m", "mwise", "nac", "noc", "nwise", "p", "ppin", "pper", "sha", "tho", "to");

        return ($syllable_1[rand(0, 21)] . $syllable_2[rand(0, 6)] . $syllable_3[rand(0, 25)]);
    }

    static function SaxonNames()
    {
        $syllable_1 = array("Ald", "Aeld", "Alf", "Aelf", "Alh", "Aelh", "Athel", "Aethel", "Beo", "Beor", "Berh", "Brih", "Briht", "Cad", "Cead", "Cen", "Coel", "Cuth", "Cyne", "Ed", "Ead", "El", "Eal", "Eld", "Eg", "Ecg", "Eorp", "God", "Guth", "Har", "Hwaet", "Leo", "Leof", "Oft", "Ot", "Oth", "Os", "Osw", "Peht", "Pleg", "Rad", "Raed", "Sig", "Sige", "Si", "Sihr", "Tat", "Tath", "Tost", "Ut", "Uht", "Ul", "Ulf", "Wal", "Walth", "Wer", "Wit", "Wiht", "Wil", "Wulf");
        $syllable_2 = array("gar", "heah", "here", "bald", "war", "weard", "wulf", "dred", "red", "stan", "wold", "tric", "ric", "wald", "mon", "wal", "walla", "wealh", "frith", "gyth", "rum", "bert", "berht", "gar", "win", "wine", "wiu", "for", "mund", "thoef", "eof", "had", "erth", "ferth", "thin", "er", "ther", "tar", "thar", "wig", "wicg", "mer", "floed", "ith", "hild", "run", "drun", "ny");

        return ($syllable_1[rand(0, 59)] . $syllable_2[rand(0, 47)]);
    }

    static function OrcNames()
    {
        $syllable_1 = array("B", "Er", "G", "Gr", "H", "P", "Pr", "R", "V", "Vr", "T", "Tr", "M", "Dr");
        $syllable_2 = array("a", "i", "o", "oo", "u", "ui");
        $syllable_3 = array("dash", "dish", "dush", "gar", "gor", "gdush", "lo", "gdish", "k", "lg", "nak", "rag", "rbag", "rg", "rk", "ng", "nk", "rt", "ol", "urk", "shnak", "mog", "mak", "rak");

        return ($syllable_1[rand(0, 13)] . $syllable_2[rand(0, 5)] . $syllable_3[rand(0, 23)]);
    }

    static function generateLogin()
    {
        $methods = array('DwarfNames', 'ElfNames', 'GnomeNames', 'HalflingNames', 'SaxonNames', 'OrcNames');
        $rm = rand(0, 5);
        $rs = rand(1000, 9999);

        $login = call_user_func('self::' . $methods[$rm]);

        return strtolower($login . $rs);
    }

}