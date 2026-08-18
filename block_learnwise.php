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

use local_learnwise\util;

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
        $settings = get_config('local_learnwise');
        $this->page->requires->js_call_amd('block_learnwise/chat', 'init', [[
            'frameId' => 'learnwise-chat-frame',
            'injectorHost' => util::get_remotehosturl(),
            'chatUrl' => util::get_ltitoolurl(),
            'assistantId' => $settings->assistantid,
            'region' => $settings->region,
            'courseId' => $this->page->course->id,
        ]]);
        return $this->content;
    }

    /**
     * Define pages where blocks can be loaded.
     * @return array
     */
    public function applicable_formats() {
        return ['all' => false, 'course' => true];
    }
}
