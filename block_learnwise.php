<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

use local_learnwise\hook_callbacks;

/**
 * Block Learnwise
 *
 * Documentation: {@link https://moodledev.io/docs/apis/plugintypes/blocks}
 *
 * @package    block_learnwise
 * @copyright  2026 LearnWise <help@learnwise.ai>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_learnwise extends block_base {
    /**
     * Block initialisation
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_learnwise');
    }

    /**
     * Get content
     *
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $html = (string) hook_callbacks::before_standard_top_of_body_html_generation();
        if (!$decodedsetup = self::extractjsobject($html)) {
            return $this->content;
        }

        $this->content = (object)[
            'footer' => '',
            'text' => <<<HTML
<div id="learnwise-sidebar-chat">
  <iframe
    id="learnwise-chat-frame"
    title="LearnWise assistant"
    allow="microphone; clipboard-write"
  ></iframe>
</div>
HTML
,
        ];
        $this->page->requires->js_call_amd('block_learnwise/chat', 'init', [[
            'frameId' => 'learnwise-chat-frame',
            'injectorHost' => $decodedsetup['host'],
            'chatUrl' => $decodedsetup['chatsrc'],
            'assistantId' => $decodedsetup['assistantid'],
            'courseId' => $decodedsetup['courseid'],
            'region' => $decodedsetup['region'],
        ]]);
        return $this->content;
    }

    /**
     * Extract js object from string and decodes it.
     * @param string $html html in which variable defined
     * @param string $varname variable to extact
     * @return array|null extracted array<key, value> or empty
     */
    public function extractjsobject(string $html, string $varname = 'window.learnWiseSetup'): ?array {
        // Grab the balanced { ... } after the assignment.
        $re = '/' . preg_quote($varname, '/') . '\s*=\s*(\{(?:[^{}]|(?1))*\})/s';
        if (!preg_match($re, $html, $m)) {
            return null;
        }
        $jsonstring = self::jstojson($m[1]);
        if (!$jsonstring) {
            return null;
        }
        $jsonarray = json_decode($jsonstring, true);
        $jsonarray = array_change_key_case($jsonarray, CASE_LOWER);
        return $jsonarray;
    }

    /**
     * Convert js object string to json string.
     * @param string $js js object string
     * @return string json object string
     */
    public function jstojson(string $js): string {
        $pattern = '~
            "(?:\\\\.|[^"\\\\])*"                    # double-quoted string
            | \'(?:\\\\.|[^\'\\\\])*\'                 # single-quoted string
            | //[^\n]*                                 # line comment
            | /\*.*?\*/                                # block comment
            | (?P<key>[A-Za-z_$][A-Za-z0-9_$]*)\s*:    # unquoted key
            | (?P<trail>,)\s*(?=[}\]])                 # trailing comma
        ~sx';

        return preg_replace_callback($pattern, function (array $m) {
            if (isset($m['key']) && $m['key'] !== '') {
                return '"' . $m['key'] . '":';
            }
            if (isset($m['trail']) && $m['trail'] !== '') {
                return '';
            }
            $tok = $m[0];
            if ($tok[0] === '/') {
                return ''; // Strip comment.
            }
            if ($tok[0] === "'") { // Single → double quoted.
                $inner = str_replace("\\'", "'", substr($tok, 1, -1));
                return json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            return $tok; // Already valid JSON string.
        }, $js);
    }

    /**
     * Define pages where blocks can be loaded.
     * @return array
     */
    public function applicable_formats() {
        return ['all' => false, 'course' => true];
    }
}
