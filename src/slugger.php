<?php

// Based on https://github.com/Flet/github-slugger/blob/master/index.js
// by Dan Flettre

namespace GitHubSlugger;

/**
 * Assumes the caller sets mb_intenral_encoding and mb_regex_encoding
 */
class Slugger {

    // Regex
    private $re_to_clear;
    private $re_emoji;

    // Internal data
    private $occurrences;   // map slug to count
    private $maintainCase;
    private $replacement;

    function __construct($maintainCase = FALSE, $replacement_char = '-') {
        $this->init_REs();
        //print_r('-' . $this->re_to_clear . '-');

        $this->maintainCase = $maintainCase;
        $this->replacement = $replacement_char;

        $this->reset();
    } //ctor

    /**
     * Generate a unique slug.
     * @param  {string} value String of text to slugify
     * @return {string}       A unique slug string
     */
    function slug($value) {
        $retval = $this->slugger($value);

        //print_r($retval);
        if (array_key_exists($retval, $this->occurrences)) {
            $occurrences = $this->occurrences[$retval] + 1;
        } else {
            $occurrences = 0;
        }

        $this->occurrences[$retval] = $occurrences;

        if ($occurrences > 0) {
            $retval = $retval . '-' . $occurrences;
        }

        return $retval;
    } //slug()

    /**
     * Reset - Forget all previous slugs
     * @return void
     */
    function reset() {
        $this->occurrences = [];
    } //reset()

    function slugger($val) {

        if (!$this->maintainCase) {
            $val = mb_ereg_replace_callback(
                '[A-Z]+',
                function($matches) {
                    return mb_strtolower($matches[0]);  //whole match
                },
                $val);
        }
        $val = trim($val);
        $val = mb_ereg_replace($this->re_to_clear, '', $val);
        $val = mb_ereg_replace($this->re_emoji, '', $val);
        $val = mb_ereg_replace('\s', $this->replacement, $val);

        return $val;
    } //slugger()

    // From commit 34013d4e06a6177a9a1118d1bec0166a93ef343a in
    // https://raw.githubusercontent.com/mathiasbynens/emoji-regex/master/index.js

    function init_REs() {
        $this->re_to_clear = <<<'EOT'
[\x{2000}-\x{206F}\x{2E00}-\x{2E7F}\\'!"#$%&()*+,.\/:;<=>?@\[\]^`\{\|\}~]
EOT;
        $this->re_to_clear = trim($this->re_to_clear);

        $this->re_emoji = <<<'EOT'
[\xA9\xAE\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}\x{21AA}\x{231A}\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}\x{2623}\x{2626}\x{262A}\x{262E}\x{262F}\x{2638}-\x{263A}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}\x{2666}\x{2668}\x{267B}\x{267F}\x{2692}-\x{2694}\x{2696}\x{2697}\x{2699}\x{269B}\x{269C}\x{26A0}\x{26A1}\x{26AA}\x{26AB}\x{26B0}\x{26B1}\x{26BD}\x{26BE}\x{26C4}\x{26C5}\x{26C8}\x{26CE}\x{26CF}\x{26D1}\x{26D3}\x{26D4}\x{26E9}\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}\x{2935}\x{2B05}-\x{2B07}\x{2B1B}\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}]|\x{D83C}[\x{DC04}\x{DCCF}\x{DD70}\x{DD71}\x{DD7E}\x{DD7F}\x{DD8E}\x{DD91}-\x{DD9A}\x{DE01}\x{DE02}\x{DE1A}\x{DE2F}\x{DE32}-\x{DE3A}\x{DE50}\x{DE51}\x{DF00}-\x{DF21}\x{DF24}-\x{DF93}\x{DF96}\x{DF97}\x{DF99}-\x{DF9B}\x{DF9E}-\x{DFF0}\x{DFF3}-\x{DFF5}\x{DFF7}-\x{DFFF}]|\x{D83D}[\x{DC00}-\x{DCFD}\x{DCFF}-\x{DD3D}\x{DD49}-\x{DD4E}\x{DD50}-\x{DD67}\x{DD6F}\x{DD70}\x{DD73}-\x{DD79}\x{DD87}\x{DD8A}-\x{DD8D}\x{DD90}\x{DD95}\x{DD96}\x{DDA5}\x{DDA8}\x{DDB1}\x{DDB2}\x{DDBC}\x{DDC2}-\x{DDC4}\x{DDD1}-\x{DDD3}\x{DDDC}-\x{DDDE}\x{DDE1}\x{DDE3}\x{DDEF}\x{DDF3}\x{DDFA}-\x{DE4F}\x{DE80}-\x{DEC5}\x{DECB}-\x{DED0}\x{DEE0}-\x{DEE5}\x{DEE9}\x{DEEB}\x{DEEC}\x{DEF0}\x{DEF3}]|\x{D83E}[\x{DD10}-\x{DD18}\x{DD80}-\x{DD84}\x{DDC0}]|\x{D83C}\x{DDFF}\x{D83C}[\x{DDE6}\x{DDF2}\x{DDFC}]|\x{D83C}\x{DDFE}\x{D83C}[\x{DDEA}\x{DDF9}]|\x{D83C}\x{DDFD}\x{D83C}\x{DDF0}|\x{D83C}\x{DDFC}\x{D83C}[\x{DDEB}\x{DDF8}]|\x{D83C}\x{DDFB}\x{D83C}[\x{DDE6}\x{DDE8}\x{DDEA}\x{DDEC}\x{DDEE}\x{DDF3}\x{DDFA}]|\x{D83C}\x{DDFA}\x{D83C}[\x{DDE6}\x{DDEC}\x{DDF2}\x{DDF8}\x{DDFE}\x{DDFF}]|\x{D83C}\x{DDF9}\x{D83C}[\x{DDE6}\x{DDE8}\x{DDE9}\x{DDEB}-\x{DDED}\x{DDEF}-\x{DDF4}\x{DDF7}\x{DDF9}\x{DDFB}\x{DDFC}\x{DDFF}]|\x{D83C}\x{DDF8}\x{D83C}[\x{DDE6}-\x{DDEA}\x{DDEC}-\x{DDF4}\x{DDF7}-\x{DDF9}\x{DDFB}\x{DDFD}-\x{DDFF}]|\x{D83C}\x{DDF7}\x{D83C}[\x{DDEA}\x{DDF4}\x{DDF8}\x{DDFA}\x{DDFC}]|\x{D83C}\x{DDF6}\x{D83C}\x{DDE6}|\x{D83C}\x{DDF5}\x{D83C}[\x{DDE6}\x{DDEA}-\x{DDED}\x{DDF0}-\x{DDF3}\x{DDF7}-\x{DDF9}\x{DDFC}\x{DDFE}]|\x{D83C}\x{DDF4}\x{D83C}\x{DDF2}|\x{D83C}\x{DDF3}\x{D83C}[\x{DDE6}\x{DDE8}\x{DDEA}-\x{DDEC}\x{DDEE}\x{DDF1}\x{DDF4}\x{DDF5}\x{DDF7}\x{DDFA}\x{DDFF}]|\x{D83C}\x{DDF2}\x{D83C}[\x{DDE6}\x{DDE8}-\x{DDED}\x{DDF0}-\x{DDFF}]|\x{D83C}\x{DDF1}\x{D83C}[\x{DDE6}-\x{DDE8}\x{DDEE}\x{DDF0}\x{DDF7}-\x{DDFB}\x{DDFE}]|\x{D83C}\x{DDF0}\x{D83C}[\x{DDEA}\x{DDEC}-\x{DDEE}\x{DDF2}\x{DDF3}\x{DDF5}\x{DDF7}\x{DDFC}\x{DDFE}\x{DDFF}]|\x{D83C}\x{DDEF}\x{D83C}[\x{DDEA}\x{DDF2}\x{DDF4}\x{DDF5}]|\x{D83C}\x{DDEE}\x{D83C}[\x{DDE8}-\x{DDEA}\x{DDF1}-\x{DDF4}\x{DDF6}-\x{DDF9}]|\x{D83C}\x{DDED}\x{D83C}[\x{DDF0}\x{DDF2}\x{DDF3}\x{DDF7}\x{DDF9}\x{DDFA}]|\x{D83C}\x{DDEC}\x{D83C}[\x{DDE6}\x{DDE7}\x{DDE9}-\x{DDEE}\x{DDF1}-\x{DDF3}\x{DDF5}-\x{DDFA}\x{DDFC}\x{DDFE}]|\x{D83C}\x{DDEB}\x{D83C}[\x{DDEE}-\x{DDF0}\x{DDF2}\x{DDF4}\x{DDF7}]|\x{D83C}\x{DDEA}\x{D83C}[\x{DDE6}\x{DDE8}\x{DDEA}\x{DDEC}\x{DDED}\x{DDF7}-\x{DDFA}]|\x{D83C}\x{DDE9}\x{D83C}[\x{DDEA}\x{DDEC}\x{DDEF}\x{DDF0}\x{DDF2}\x{DDF4}\x{DDFF}]|\x{D83C}\x{DDE8}\x{D83C}[\x{DDE6}\x{DDE8}\x{DDE9}\x{DDEB}-\x{DDEE}\x{DDF0}-\x{DDF5}\x{DDF7}\x{DDFA}-\x{DDFF}]|\x{D83C}\x{DDE7}\x{D83C}[\x{DDE6}\x{DDE7}\x{DDE9}-\x{DDEF}\x{DDF1}-\x{DDF4}\x{DDF6}-\x{DDF9}\x{DDFB}\x{DDFC}\x{DDFE}\x{DDFF}]|\x{D83C}\x{DDE6}\x{D83C}[\x{DDE8}-\x{DDEC}\x{DDEE}\x{DDF1}\x{DDF2}\x{DDF4}\x{DDF6}-\x{DDFA}\x{DDFC}\x{DDFD}\x{DDFF}]|[#\*0-9]\x{20E3}
EOT;
        $this->re_emoji = trim($this->re_emoji);
    } //init_emoji()

};

/*
Emoji regex copyright:
Copyright Mathias Bynens <https://mathiasbynens.be/>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.*/

// vi: set ts=4 sts=4 sw=4 et ai: //
